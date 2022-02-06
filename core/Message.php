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
        Session::set('__MESSAGES__', self::$message);
        // vdump(debug_backtrace());
    }

    public static function loadMessageError(){
        if($message = Session::get('__MESSAGES__')){
            $errors = '';
            foreach($message as $type => $error){
                $_type = '!' . ucfirst(self::$message_type[$type]??$type);
                $errors .= <<<HTML
                    <h2 class='bg-$type py-2 mb-0 ps-3 text-white'>$_type</h2>
                HTML;
                $errors .= "<ul class='bg-$type py-2 mb-0 text-white'>";
                foreach($error as $description){
                    $errors .= $description;
                }
                $errors .= '</ul>';
            }
            Session::set('__ERROR__', $errors);
            Session::destroy('__MESSAGES__');
            if(!Session::auth()){
                reload('/' . PAGE_INIT);
            }elseif($route = Route::get(Session::get('__CURRENT_ROUTE__'))){
                if(!$route['auth']) reload('/' . PAGE_ACCESS_AUTH);
                else reload(Session::get('__CURRENT_ROUTE__') ?? Session::get('__LAST_ROUTE__'));
            }
        }else{
            Html::addVariable('MESSAGE', Session::get('__ERROR__'));
            Session::destroy('__ERROR__');
        }
    }

    public static function exist(){
        return Session::get('__MESSAGES__') != null;
    }

    public static function clear(){
        self::$message = [];
    }
}