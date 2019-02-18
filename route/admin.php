<?php

Route::group('auth', function () {
    Route::get('/passport/login', 'auth\\Passport@login')->name('tadmin.auth.passport.login');
    Route::post('/passport/login', 'auth\\Passport@loginAuth');

    Route::get('/passport/logout', 'auth\\Passport@logout')->name('tadmin.auth.passport.logout')->middleware('tadmin.admin');
    Route::get('/passport/user', 'auth\\Passport@user')->name('tadmin.auth.passport.user')->middleware('tadmin.admin');
});

Route::group([
    'middleware' => ['tadmin.admin'],
], function () {
    Route::group('auth', function () {
        // 管理员
        Route::get('/adminer/delete', 'auth\\Adminer@delete')->name('tadmin.auth.adminer.delete');
        Route::get('/adminer/edit', 'auth\\Adminer@edit')->name('tadmin.auth.adminer.edit');
        Route::post('/adminer/edit', 'auth\\Adminer@save');
        Route::get('/adminer', 'auth\\Adminer@index')->name('tadmin.auth.adminer');

        // 角色
        Route::get('/role/delete', 'auth\\Role@delete')->name('tadmin.auth.role.delete');
        Route::get('/role/edit', 'auth\\Role@edit')->name('tadmin.auth.role.edit');
        Route::post('/role/edit', 'auth\\Role@save');
        Route::get('/role', 'auth\\Role@index')->name('tadmin.auth.role');

        // 权限
        Route::get('/permission/delete', 'auth\\Permission@delete')->name('tadmin.auth.permission.delete');
        Route::get('/permission/edit', 'auth\\Permission@edit')->name('tadmin.auth.permission.edit');
        Route::post('/permission/edit', 'auth\\Permission@save');
        Route::get('/permission', 'auth\\Permission@index')->name('tadmin.auth.permission');

        Route::get('/log', 'auth\\Log@index')->name('tadmin.auth.log');
    });

    // 首页
    Route::get('/', 'Index@index')->name('tadmin.index');
    Route::get('/dashboard', 'Index@index');

    Route::get('/config/add', 'Config@add')->name('tadmin.config.add');
    Route::post('/config/add', 'Config@create');
    Route::get('/config', 'Config@index')->name('tadmin.config');
    Route::post('/config', 'Config@save');


    Route::any('/upload/image', 'Upload@image')->name('tadmin.upload.image');
    Route::any('/upload/ueditor', 'Upload@ueditor')->name('tadmin.upload.ueditor');
});
