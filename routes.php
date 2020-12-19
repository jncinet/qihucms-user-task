<?php

use Illuminate\Routing\Router;

// 接口
Route::group([
    'prefix' => config('qihu.user_task_prefix', 'task'),
    'namespace' => 'Qihucms\UserTask\Controllers\Api',
    'middleware' => ['api'],
    'as' => 'api.task.'
], function (Router $router) {
    $router->get('select-tasks', 'TaskController@findTaskByQ')->name('select');
    $router->apiResource('tasks', 'TaskController');
    $router->get('tasks/user', 'TaskController@userIndex')->name('tasks.user.index');
    $router->get('tasks/user/{id}', 'TaskController@userShow')->name('tasks.user.show');
    $router->apiResource('orders', 'OrderController');
    $router->post('orders/audit/{id}', 'OrderController@audit')->name('orders.audit');
});

// 后台
Route::group([
    'prefix' => config('admin.route.prefix') . '/task',
    'namespace' => 'Qihucms\UserTask\Controllers\Admin',
    'middleware' => config('admin.route.middleware'),
    'as' => 'admin.'
], function (Router $router) {
    $router->resource('tasks', 'TaskController');
    $router->resource('orders', 'OrderController');
});