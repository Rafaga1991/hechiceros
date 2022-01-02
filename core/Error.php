<?php

trait Errors{
    private static $errors = [];

    public static function add($type, $title){
        self::$errors[$type][] = $title;
    }

    public static function exist(&$view){
        $acum = '';
        foreach(self::$errors as $index => $error){
            $index = strtolower($index);
            $acum .= '<div container="error">';
            foreach($error as $value){
                $acum .= <<<HTML
                    <div error='$index'> * $index: $value</div>
                HTML;
            }
            $acum .= '</div>';
        }

        $view = empty(self::$errors)? $view : $acum;

        return !empty(self::$errors);
    }
}