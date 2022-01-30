<?php

class Controller{
    private $object = null;

    /**
     * Constructor de la clase, toma el objeto hijo y lo asigna a la variable objeto.
     * 
     * @access public
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function __construct()
    {
        $this->object = debug_backtrace()[0]['object'];
    }

    /**
     * Verifica si un objeto y una función existen.
     * 
     * @access private
     * @param string $controller recive el controlador u objeto a verificar su existencia.
     * @param string $function recive el nombre de la función que será verificada si existe en el objeto pasado.
     * @return bool retorna un buleano indicando si existe o no la funcion y el objeto.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private function objectExists(string $controller, string $function):bool{
        $exist = false;
        if(class_exists($controller)){
            if(method_exists($controller, $function)) $exist = true;
            else Message::add("La función <b>$function</b> no existe");
        }else{
            Message::add("La clase <b>$controller</b> no existe");
        }
        return $exist;
    }

    /**
     * Retorna una vista de un contralador recivido
     * 
     * @access protected
     * @param array $action recive un arreglo que contiene el controlador y la función.
     * @param mixed $data recive los datos que seran enviados a la función.
     * @return string retorna una vista.
     */
    protected function view(array $action, $data=null):string{
        if($this->objectExists($action[0], $action[1])){
            ${$action[0]} = new $action[0]();
            if($data){
                return ${$action[0]}->{$action[1]}($data);
            }else{
                return ${$action[0]}->{$action[1]}();
            }
        }
        return '';
    }

    /**
     * Redirecciona a otra vista del mismo controlador.
     * 
     * @access protected
     * @param string $function recive el nombre de la función a redirigir.
     * @param mixed $data recive los datos a enviar a la función.
     * @return string retorna una vista.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    protected function redirect(string $function, $data = null):string{
        if($this->object){
            if($this->objectExists(get_class($this->object), $function)){
                if($data) return $this->object->{$function}($data);
                return $this->object->{$function}();
            }
        }
        return '';
    }
}