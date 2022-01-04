<?php

function dump($value, $die=true) { Dump::show($value, $die); }

function vdump($value, $die=true){
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    if($die) exit();
}