<?php

class ListController extends Controller{
    public function index(){
        return $this->view('list', Session::get('clan-info'));
    }
}