<?php

class HomeController extends Controller{
    private $view = 'home/index';
    private $activity = null;

    public function __construct()
    {
        $this->view = view($this->view);
        $this->activity = new activity();
    }

    public function index(){
        Html::addVariable('body', view('home/home'));
        return $this->view;
    }

    public function activity(){
        if(Session::getRol() != Route::ROL_ADMIN) Route::reload('home.index');
        Html::addVariable('body', view('home/option/activity', ['activity' => $this->activity->get()]));
        return $this->view;
    }

    public function setting(){
        Html::addVariables(['body' => view('home/option/setting')]);
        return $this->view;
    }

    public function update(Request $request){
        if($request->tokenIsValid()){
            vdump($request->getData());
        }
        return $this->setting();
    }
}