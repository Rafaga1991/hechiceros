<?php

namespace core;

class File{
    /** @var TYPE_SIZE_DEFAULT tamaño del archivo por defecto. */
    public const TYPE_SIZE_DEFAULT = 1;
    /** @var TYPE_SIZE_KB tamaño del archivo en kilobyte. */
    public const TYPE_SIZE_KB = 1;
    /** @var TYPE_SIZE_MB tamaño del archivo en megabyte. */
    public const TYPE_SIZE_MB = 2;
    /** @var TYPE_SIZE_GB tamaño del archivo en gigabyte. */
    public const TYPE_SIZE_GB = 3;
    /** @var TYPE_SIZE_TB tamaño del archivo en terabyte. */
    public const TYPE_SIZE_TB = 4;

    /** @var TYPE_SIZE tipos de tamaños de archivos. */
    private const TYPE_SIZE = ['KB', 'MB', 'GB', 'TB'];

    public function __construct($file) { foreach($file as $name => $value) $this->{$name} = $value; }

    /**
     * retorna el tamaño del archivo.
     * 
     * @access public
     * @param int $type_size recive el tipo del tamaño del archivo a mostrar.
     * @param bool $number recive un buleano para renortan entero sin el nombre del tipo de tamaño.
     * @return string retorna el tamaño del archivo.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function getSize(int $type_size = self::TYPE_SIZE_KB, bool $number = false):string{
        $size = $this->size;
        for($i=0; $i<$type_size; $i++){
            $size /= 1024;
        }

        $size = round($size, 2);
        
        if(isset(self::TYPE_SIZE[--$type_size])){
            if($number) return (string)$size;
            return "$size " . self::TYPE_SIZE[$type_size];
        }

        return '';
    }

    /**
     * Mueve un archivo a otra carpeta.
     * 
     * @access private
     * @param string $path recive la ruta donde será movido.
     * @param string $filename recive el nombre del archivo.
     * @param string $dir recive el nombre de la carpeta donde se colocará el archivo.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private function __move(string $path, string $filename=null, string $dir=null):void{
        $filename = trim($filename ?? $this->name);
        $pathinfo = pathinfo($filename);
        if(!isset($pathinfo['extension']))$pathinfo = pathinfo($this->name);
        $filename .= isset($pathinfo['extension']) ? ".{$pathinfo['extension']}" : '';
        $dir = join('/', array_merge(array_filter(explode('/', $dir ?? '/')), [$filename]));
        $dir = "$path/$dir";
        move_uploaded_file($this->tmp_name, $dir);
    }

    /**
     * Mueve un archivo a la carpeta asset por defecto.
     * 
     * @access private
     * @param string $filename recive el nombre del archivo.
     * @param string $dir recive el nombre de la carpeta donde se colocará el archivo.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function moveFileToAsset(string $filename=null, string $dir=null):void{
        $this->__move(Functions::getRoute(ASSET_DIR_NAME), $filename, $dir);
    }

    /**
     * Mueve un archivo a cualquier otra carpeta especificada.
     * 
     * @access private
     * @param string $filename recive el nombre del archivo.
     * @param string $dir recive el nombre de la carpeta donde se colocará el archivo.
     * @return void sin retorno.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public function moveFileTo(string $dirname, string $filename=null):void{
        $this->__move(Functions::getRoute(), $filename, $dirname);
    }
}