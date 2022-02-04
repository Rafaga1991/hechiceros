<?php

$route->set('/', [LoginController::class, 'index'])->name('login.index')->save();
$route->set('/access', [LoginController::class, 'access'])->name('login.access')->save();
$route->set('/logout', [LoginController::class, 'logout'])->name('login.logout')->auth()->save();

$route->set('/home', [HomeController::class, 'index'])->name('home.index')->auth()->save();
$route->set('/activity', [HomeController::class, 'activity'])->name('home.activity')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/setting', [HomeController::class, 'setting'])->name('home.setting')->auth()->save();
$route->set('/setting-update', [HomeController::class, 'update'])->name('home.setting.form')->auth()->save();
$route->set('/chart-area-donations', [HomeController::class, 'chartAreaDonations'])->auth()->save();
$route->set('/chart-bar-perfomance', [HomeController::class, 'chartBarPerfomance'])->auth()->save();
$route->set('/reload', [HomeController::class, 'reload'])->name('home.reload')->auth()->save();

$route->set('/currentwar', [CurrentWarController::class, 'index'])->name('currentwar.index')->auth()->save();
$route->set('/cw-reload', [CurrentWarController::class, 'reload'])->name('currentwar.reload')->auth()->save();