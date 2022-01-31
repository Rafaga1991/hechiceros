<?php

$route->set('/', [LoginController::class, 'index'])->name('login.index')->save();
$route->set('/access', [LoginController::class, 'access'])->name('login.access')->save();
$route->set('/logout', [LoginController::class, 'logout'])->name('login.logout')->auth()->save();

$route->set('/home', [HomeController::class, 'index'])->name('home.index')->auth()->save();
$route->set('/activity', [HomeController::class, 'activity'])->name('home.activity')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/setting', [HomeController::class, 'setting'])->name('home.setting')->auth()->save();
$route->set('/setting-update', [HomeController::class, 'update'])->name('home.setting.form')->auth()->save();
