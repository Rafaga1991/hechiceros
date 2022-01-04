<?php

class Controller{
    private function loadPage($path, $value=''){
        ob_start();
        extract($value, EXTR_PREFIX_SAME, 'dta');
        include $path;
        $ob = ob_get_contents();
        ob_clean();
        return $ob;
    }

    protected function view($path, $value=''){
        $path = "./view/$path.php";
        $page = '';
        if(file_exists($path)){
            $page = $this->loadPage($path, $value);
        }else{
            $page = $this->loadPage('./view/error/500.php');
        }

        return $page;
    }

    protected function redirect($path='.', $value=''){
        if($path != '.'){
            $data = array_filter(explode('.', $path));
            $variable = strtolower($data[0]);
            $class = ucfirst($variable) . 'Controller';
            $$variable = new $class();
            $fun = $data[1];
            if(empty($value)){
                $$variable->$fun();
            }else{
                $$variable->$fun($value);
            }
        }else{
            return debug_backtrace()[0]['object']->index();
        }
    }
}