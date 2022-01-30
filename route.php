<?php

$route = new Route();

$route->set('/', [LoginController::class, 'index'])->name('login')->save();
$route->set('login/show', [LoginController::class, 'showData'])->name('login.showData')->save();