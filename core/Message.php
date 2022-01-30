<?php

class Message{
    private static $message = [];
    private static $message_type = [
        'warning' => 'advertencia',
        'danger' => 'peligro',
        'success' => 'Exito'
    ];

    public static function add(string $description, string $type = 'warning'){
        self::$message[$type][] = <<<HTML
            <li>$description</li>
        HTML;
    }

    public static function showMessage(){
        if(!empty(self::$message)){
            $show_btn = true;
            foreach(self::$message as $type => $error){
                $_type = '!' . ucfirst(self::$message_type[$type]??$type);
                echo <<<HTML
                    <h2 class='bg-$type py-2 mb-0 ps-3 text-white'>$_type</h2>
                HTML;
                echo "<ul class='bg-$type py-2 mb-0 text-white'>";
                foreach($error as $description){
                    echo $description;
                }
                echo '</ul>';
                if(in_array($type, ['success'])){
                    $show_btn = false;
                }
            }
            
            if($show_btn){
                echo <<<HTML
                    <a href='/' class='btn btn-outline-primary my-3 ms-3'><i class="fas fa-arrow-left"></i> Volver</a>
                HTML;
            }
        }
    }

    public static function exist(){
        return !empty(self::$message);
    }

    public static function clear(){
        self::$message = [];
    }
}