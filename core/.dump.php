<?php

trait Dump
{
    private static $arrays = 0;
    private static $positions = 0;
    private static $keys = 0;

    private static $body_backgroundColor = '#4F4F4F';
    private static $all_fontSize = '13px';
    private static $borderContainer = '1px solid gray';

    /**
     * recursivamente busca los valores e indices de los arreglos
     *
     * @access public
     * @param array $value recive un arreglo o valor a ser investigado.
     * @return string retorna código html con los datos recividos.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    private static function __show($value): string
    {
        self::$arrays++;
        $data = '';
        foreach ($value as $index => $val) {
            if (gettype($val) == 'array') {
                $arrays = self::$arrays;
                $positions = self::$positions;

                // flecha para ocultar y mostrar arreglos
                $showHidden = <<<HTML
                    <i arrow='right' cursor-style='pointer' onclick='onClickShowHidden("a${arrays}p${positions}", this)'></i>
                HTML;

                $arr = self::__show($val);
                $length = count($val);
                $data .= <<<HTML
                    <div container='normal'>
                        <span color='red' title='Arreglo P&uacute;blico' cursor-style='pointer'>+</span>
                        <span color='gray'>$index:</span> Array ($length)
                        {
                            $showHidden
                            <span container='hidden' id="a${arrays}p${positions}">$arr</span>
                        }
                    </div>
                HTML;
            } elseif (gettype($val) != 'object') {
                self::$positions++;
                $val = self::__value($val);
                $INDEX = is_numeric($index) ? "<span color='red'>$index:</span> " : "<span color='red' title='Atributo P&uacute;blico' cursor-style='pointer'>+ </span><span color='gray'>$index</span>:";
                $data .= <<<HTML
                    <div container='normal'>$INDEX $val</div>
                HTML;
            } elseif (gettype($val) == 'object') {
                $length = 0;
                $obj = self::__htmlObject($val, self::__objClass($val, $length));
                if($val instanceof stdClass){
                    $obj = <<<HTML
                        Object ($length) {
                            $obj
                        }
                    HTML;
                }
                $data .= <<<HTML
                    <div container='normal'>
                        <span color='red' title='Atributo P&uacute;blico' cursor-style='pointer'>+</span>    
                        <span color='gray'>$index</span>: $obj
                    </div>
                HTML;
            }
        }

        return $data;
    }

    /**
     * Busca y diseña el indice de un arreglo.
     *
     * @access public
     * @param array $array recive el arreglo bidimencional que será diseñado.
     * @param string $title recive el titulo que sera el placeholder en el tipo de dato.
     * @param string $sign recive el signo que del tipo de dato.
     * @param boolean $func recive un valor booleando en si será o no una función.
     * @param ReflectionClass $reflection recive un objeto reflector para una clase.
     * @param object $obj recive un objeto para extraer el valor de sus variables.
     * @return string return un html de los indicies rediseñados.
     * @author Rafael Minana
     * @copyright R.M.B
     * @version 1.0
     */
    private static function __htmlIndex($array, $title, $sign, $func=true, $reflection=null, $obj=null)
    {
        $function = '';
        foreach ($array as $value) {
            self::$positions++;
            $type = $func?'Function':gettype($value->name);
            $valueProperty = '';
            if($reflection != null && !$func){
                $reflect = $reflection->getProperty($value->name);
                $reflect->setAccessible(true);
                $val = $reflect->getValue($obj);
                $html = self::__value($val);
                $function .= <<<HTML
                    <div>
                        <span color='red' title='$title' cursor-style='pointer'>$sign</span>
                        <span color='gray'>$value->name:</span>
                        $html
                    </div>
                HTML;
            }else{
                $function .= <<<HTML
                    <div>
                        <span color='red' title='$title' cursor-style='pointer'>$sign</span>
                        <span color='gray'>$value->name:</span>
                        <span>$valueProperty</span>
                        <span color='blue' title='$title' cursor-style='pointer'>
                        $type
                        </span>
                    </div>
                HTML;
            }
        }

        return $function;
    }

    /**
     * Crea y diseña html para una clase u objeto.
     *
     * @access public
     * @param object $value resive un onjeto.
     * @param int $lengthParam hace referencia y se le asigna la cantidad de parametros.
     * @return string retorna html del objeto diseñado.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    private static function __objClass($value, &$lengthParam=0)
    {
        if ($value instanceof stdClass) {
            $params = get_object_vars($value);
            $data = self::__show($params);
            $lengthParam = count($params);
            return $data;
        } else {
            $reflection = new ReflectionClass($value);

            $params = '';
            $function = '';

            $params .= self::__htmlIndex(
                $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
                'Atributo P&uacute;blico.',
                '+',
                false,
                $reflection,
                $value
            );

            $params .= self::__htmlIndex(
                $reflection->getProperties(ReflectionProperty::IS_PRIVATE),
                'Atributo Privado.',
                '-',
                false,
                $reflection,
                $value
            );

            $params .= self::__htmlIndex(
                $reflection->getProperties(ReflectionProperty::IS_PROTECTED),
                'Atributo Protegido.',
                '#',
                false,
                $reflection,
                $value
            );

            $function .= self::__htmlIndex(
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                'Funci&oacute;n P&uacute;blica.',
                '+'
            );

            $function .= self::__htmlIndex(
                $reflection->getMethods(ReflectionMethod::IS_PRIVATE),
                'Funci&oacute;n Privada.',
                '-'
            );

            $function .= self::__htmlIndex(
                $reflection->getMethods(ReflectionMethod::IS_PROTECTED),
                'Funci&oacute;n Protegida.',
                '#'
            );

            return <<<HTML
                <div container='normal'>$params</div>
                <div container='normal'>$function</div>
            HTML;
        }
    }

    /**
     * Busca informacón de un valor especifico
     *
     * @access public
     * @param string $value recive valor a ser investigado.
     * @return string retorna código html con datos del valor recivido.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    private static function __value($value): string
    {
        $type = gettype($value);

        $data = <<<HTML
            <span color='blue'>null</span>
        HTML;

        if ($type == 'integer') {
            $data = <<<HTML
                <span color='green'>$type</span>(<span color='red'>$value</span>)
            HTML;
        } elseif ($type == 'double') {
            $data = <<<HTML
                <span color='green'>$type</span>(<span color='red'>$value</span>)
            HTML;
        } elseif ($type == 'string') {
            $value = strip_tags($value);
            $positions = self::$positions;
            $strlen = strlen($value);
            $val = ($strlen > 25) ? substr($value, 0, 25) : $value;
            $value = ($strlen > 25) ? substr($value, 25, strlen($value)) : $value;
            if ($strlen > 25) {
                $val = <<<HTML
                    $val<a href='javascript:onClickShowText("m$positions", "a$positions")' id="a$positions" title='Mostrar Texto'>{...}</a><span id='m$positions' hidden>$value</span>
                HTML;
            }
            $val = trim($val);
            $data = <<<HTML
                "$val" 
                <span color='blue'>$type<span color='black'>(</span><span color='green'>$strlen</span><span color='black'>)</span></span>
            HTML;
        } elseif ($type == 'boolean') {
            $value = $value ? 'true' : 'false';
            $data = <<<HTML
                <span color='blue'>$type</span>(<span color='blue'>$value</span>)
            HTML;
        }

        return $data;
    }

    /**
     * Carga los estilos necesarios para la página
     *
     * @access public
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    private static function loadCss(): void
    {
        $backgroundColor = self::$body_backgroundColor;
        $fontSize = self::$all_fontSize;
        $borderContainer = self::$borderContainer;

        echo <<<HTML
            <style>
                *{
                    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
                    padding: 0%;
                    margin: 0%;
                    font-size: $fontSize;
                }

                body{
                    background-color: $backgroundColor;
                }

                [color~=red]{
                    color: red;
                }

                [color~=blue]{
                    color: blue;
                }

                [color~=green]{
                    color: green;
                }

                [color~=black]{
                    color: black;
                }

                [color~=gray]{
                    color: #4F4F4F;
                }

                [container~=normal]{
                    padding: 0 0 0 20px;
                    background-color: white;
                }

                [container~=fluid]{
                    /* padding: 10px 0 10px 0; */
                    background-color: white;
                    border: $borderContainer;
                }

                [container~=hidden]{
                    display: none;
                }

                [container~=show]{
                    display: inline;
                }

                [container~=header], [container~=body], [container~=footer]{
                    padding: .5rem;
                }

                [container~=header], [container~=footer]{
                    background-color: #F0F0F0;
                }

                [container~=body]{
                    padding-left: 0;
                }

                [arrow] {
                    border: solid gray;
                    border-width: 0 2px 2px 0;
                    display: inline-block;
                    padding: 2px;
                }

                [arrow~=right] {
                    --rotate-grade: -45deg;
                    transform: rotate(var(--rotate-grade));
                    -webkit-transform: rotate(var(--rotate-grade));
                    -moz-transform: rotate(var(--rotate-grade));
                    -o-transform: rotate(var(--rotate-grade));
                    -ms-transform: rotate(var(--rotate-grade));
                    margin: 0 4px 1.2px 0;
                }

                [arrow~=down] {
                    --rotate-grade: 45deg;
                    transform: rotate(var(--rotate-grade));
                    -webkit-transform: rotate(var(--rotate-grade));
                    -moz-transform: rotate(var(--rotate-grade));
                    -o-transform: rotate(var(--rotate-grade));
                    -ms-transform: rotate(var(--rotate-grade));
                    margin: 0 0 3px 3px;
                }

                [content]{
                    padding: 1rem 1rem 0 1rem;
                }

                [text-size-1]{
                    font-size: 14px;
                    font-weight: 700;
                }

                [cursor-style~=pointer]{
                    cursor: pointer;
                }
            </style>
        HTML;
    }

    /**
     * Asigna diseño para mostrar u ocultar un arreglo.
     *
     * @access public
     * @param object $obj resive un objeto a diseñar.
     * @param string $html recive html en cadena de texto.
     * @return string retorna un string como html del objeto diseñado.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    private static function __htmlObject($obj, $html): string
    {
        $positions = self::$positions;
        // flecha para ocultar y mostrar arreglos
        $showHidden = <<<HTML
            <i arrow='right' cursor-style='pointer' onclick='onClickShowHidden("object${positions}", this)'></i>
        HTML;

        if (!($obj instanceof stdClass)) {
            $className = get_class($obj);
            return <<<HTML
                <span color='red'>class</span> $className { $showHidden
                    <span container='hidden' id="object${positions}">
                        $html
                    </span>
                }
            HTML;
        }else{
            return <<<HTML
                $showHidden
                <span container='hidden' id="object${positions}">
                    $html
                </span>
            HTML;
        }

    }

    /**
     * Carga el script para la página
     *
     * @access public
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    private static function loadScript(): void
    {
//        echo <<<HTML
//            <script>
//                function onClickShowHidden(element, arrow){
//                    element = document.getElementById(element);
//                    if(element.getAttribute('container') == 'show'){
//                        arrow.setAttribute('arrow', 'right');
//                        element.setAttribute('container', 'hidden');
//                    }else if(element.getAttribute('container') == 'hidden'){
//                        arrow.setAttribute('arrow', 'down');
//                        element.setAttribute('container', 'show');
//                    }
//                }
//
//                function onClickShowText(element, content){
//                    document.getElementById(content).hidden = true;
//                    document.getElementById(element).hidden = false;
//                }
//            </script>
//        HTML;
    }

    /**
     * Muestra información del valor recivido
     *
     * @access public
     * @param array|string|int|double|object $value resive un valor a ser investigado.
     * @param boolean $die recive valor buleano para detener o continuar el código.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B
     * @version 1.0
     */
    public static function show($value, $die=true): void
    {
        if($die){
            ob_clean();
        }else{
            self::$body_backgroundColor = 'transparent';
        }
        self::loadCss();

        $type = gettype($value);

        $positions = self::$positions;
        $showHidden = <<<HTML
            <i arrow='down' cursor-style='pointer' onclick='onClickShowHidden("type${positions}", this)'></i>
        HTML;
        if ($type == 'array') {
            $data = self::__show($value);
            $length = count($value);
            $data = <<<HTML
                <div container='normal'>
                    Array ($length) {
                        $showHidden
                        <span id='type${positions}' container='show'>
                            $data
                        </span>
                    }
                </div>
            HTML;
        } elseif ($type != 'object') {
            $data = self::__value($value);
        } else if ($type === 'object') {
            $length = 0;
            $data = self::__htmlObject($value, self::__objClass($value, $length));
            if($value instanceof stdClass){
                $data = <<<HTML
                    Object ($length){
                        $data
                    }
                HTML;
            }
        }

        $hostaName = strtoupper(gethostname());
        $debugs = debug_backtrace();

        ksort($debugs);

        $header = '';
        foreach($debugs as $key => $debug){
            $file = $debug['file'];
            $line = $debug['line'];
            if($key == (count($debugs)-1)){
                $header .= <<<HTML
                    <div><b>$file: $line</b></div>
                HTML;
            }else{
                $header .= <<<HTML
                    <div>$file: $line</div>
                HTML;
            }
        }

        echo <<<HTML
            <div content>
                <div container='fluid'>
                    <div container='header'>
                        $header
                    </div>
                    <div container='body'>$data</div>
                    <div container='footer'>
                        Server: $hostaName
                    </div>
                </div>
            </div>
        HTML;

        self::loadScript();

        if($die){
            exit;
        }
    }
}
