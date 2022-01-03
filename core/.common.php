<?php

trait Common{
    public static function dirSearch($path){
        $files = [];
        $dir = opendir($path);
        while($file = readdir($dir)){
            $tmp_file = "$path/$file";
            if($file != '.' && $file != '..'){
                if($file[0] != '.'){
                    if(is_file($tmp_file)){
                        $files[] = $tmp_file;
                    }else{
                        $files = array_merge($files, self::dirSearch($tmp_file));
                    }
                }
            }
        }
        return $files;
    }

    public static function generateToken(&$ref=''){
        $ref = substr(md5(time() . rand(0, 10)), 0, 10);
        Session::setTemp('__token', $ref);
        return <<<HTML
            <input type="hidden" value="$ref" name="__token" id="__token" />
        HTML;
    }
}