<?php

class Errors{
    private static $errors = [];

    public static function add(string $description, string $type = 'warning'){
        self::$errors[$type][] = <<<HTML
            <li>$description</li>
        HTML;
    }

    public static function showErrors(){
        if(!empty(self::$errors)){
            foreach(self::$errors as $type => $error){
                echo <<<HTML
                    <h2 class='bg-$type py-2 mb-0 ps-3'>$type</h2>
                HTML;
                echo "<ul class='bg-$type py-2 mb-0'>";
                foreach($error as $description){
                    echo $description;
                }
                echo '</ul>';
            }
        }
    }
}