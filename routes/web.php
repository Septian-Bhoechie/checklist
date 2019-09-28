<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function ($router) {
    $router->post('user/login', 'UserController@authenticate');
});

$router->group(['prefix' => 'api', 'middleware' => 'auth'], function ($router) {
    $router->get('user/show/{user_id}', 'UserController@show');

    $router->get('checklists/templates', 'TemplateController@index');
    $router->post('checklists/templates', 'TemplateController@store');
    $router->get('checklists/templates/{template_id}', 'TemplateController@show');
});
