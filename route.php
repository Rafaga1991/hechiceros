<?php

$route->set('/', [LoginController::class, 'index'])->name('login.index')->save();
$route->set('/access', [LoginController::class, 'access'])->name('login.access')->save();

$route->set('/home', [HomeController::class, 'index'])->name('home.index')->auth()->save();
