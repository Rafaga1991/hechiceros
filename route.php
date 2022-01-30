<?php

$route->set('/', [HomeController::class, 'index'])->name('home.index')->save();
$route->set('/people/client', [HomeController::class, 'show'])->name('home.show')->save();
