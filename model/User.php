<?php

namespace model;

use core\Model;

class User extends Model{ 
    public function exist(string $username){
        return !empty(parent::getQuery("SELECT * FROM `user` WHERE `username`='$username'"));
    }
}