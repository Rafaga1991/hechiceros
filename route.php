<?php

namespace core;

use controller\home\{HomeController,CurrentWarController,ListController,WarLogController};
use controller\login\{LoginController, RegisterController, UserController};

/** @var $route Route */
$route->set('/', [LoginController::class, 'index'])->name('login.index')->save();
$route->set('/access', [LoginController::class, 'access'])->name('login.access')->save();
$route->set('/logout', [LoginController::class, 'logout'])->name('login.logout')->auth()->save();

$route->set('/register', [RegisterController::class, 'index'])->name('register.index')->save();
$route->set('/new', [RegisterController::class, 'create'])->name('register.new')->save();

$route->set('/home', [HomeController::class, 'index'])->name('home.index')->auth()->save();
$route->set('/activity', [HomeController::class, 'activity'])->name('home.activity')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/setting', [HomeController::class, 'setting'])->name('home.setting')->auth()->save();
$route->set('/setting-update', [HomeController::class, 'update'])->name('home.setting.form')->auth()->save();
$route->set('/chart-area-donations', [HomeController::class, 'chartAreaDonations'])->auth()->save();
$route->set('/chart-bar-perfomance', [HomeController::class, 'chartBarPerfomance'])->auth()->save();
$route->set('/reload', [HomeController::class, 'reload'])->name('home.reload')->auth()->save();

$route->set('/currentwar', [CurrentWarController::class, 'index'])->name('currentwar.index')->auth()->save();
$route->set('/cw-reload', [CurrentWarController::class, 'reload'])->name('currentwar.reload')->auth()->save();
$route->set('/currentwar/perfomance', [CurrentWarController::class, 'perfomance'])->name('currentwar.perfomance')->auth()->save();

$route->set('/list-war', [ListController::class, 'listWar'])->name('list.war')->auth()->save();
$route->set('/list-war/new', [ListController::class, 'newListWar'])->name('list.war.new')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-war/create', [ListController::class, 'listWarCreate'])->name('list.war.create')->auth()->save();
$route->set('/list-war/show', [ListController::class, 'listWarShow'])->name('list.war.show')->auth()->save();
$route->set('/list-war/destroy', [ListController::class, 'listWarDestroy'])->name('list.war.destroy')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-war/update', [ListController::class, 'listWarUpdate'])->name('list.war.update')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-war/change', [ListController::class, 'listWarChange'])->name('list.war.change')->auth()->rol(Route::ROL_ADMIN)->save();

$route->set('/list-break', [ListController::class, 'listBreak'])->name('list.break')->auth()->save();
$route->set('/list-break/new', [ListController::class, 'listBreakNew'])->name('list.break.new')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-break/change', [ListController::class, 'listBreakChange'])->name('list.break.change')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-break/destroy', [ListController::class, 'listBreakDestroy'])->name('list.break.destroy')->auth()->rol(Route::ROL_ADMIN)->save();

$route->set('/list-wait', [ListController::class, 'listWait'])->name('list.wait')->auth()->save();
$route->set('/list-wait/new', [ListController::class, 'listWaitNew'])->name('list.wait.new')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-wait/change', [ListController::class, 'listWaitChange'])->name('list.wait.change')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/list-wait/destroy', [ListController::class, 'listWaitDestroy'])->name('list.wait.destroy')->auth()->rol(Route::ROL_ADMIN)->save();

$route->set('/warlog', [WarLogController::class, 'index'])->name('warlog.index')->auth()->save();
$route->set('/warlog/reload', [WarLogController::class, 'reload'])->name('warlog.reload')->auth()->save();
$route->set('/warlog/last-war', [WarLogController::class, 'lastWar'])->name('warlog.last')->auth()->save();

$route->set('/user', [UserController::class, 'index'])->name('user.index')->auth()->rol(Route::ROL_ADMIN)->save();
$route->set('/user/update', [UserController::class, 'update'])->name('user.update')->auth()->rol(Route::ROL_ADMIN)->save();