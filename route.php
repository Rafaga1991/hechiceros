<?php

Route::set('home/index', 'home', true);

Route::set('login/close', 'login.close', true);
Route::set('login/index', 'login');
Route::set('login/isUser', 'access');

Route::set('list/index', 'list', true);
Route::set('listMembers/index', 'list-members', true);

View::setView('menu', 'layout/menu', true, Session::get('clan-info'));
