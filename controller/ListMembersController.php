<?php

class ListMembersController extends Controller{
    public function index(){
        return $this->view('member-list', Session::get('clan-info'));;
    }
}