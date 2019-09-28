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
    $router->get('checklists/templates/{template_id}', ['as' => 'api.templates.show', 'uses' => 'TemplateController@show']);
    $router->patch('checklists/templates/{template_id}', 'TemplateController@update');
    $router->delete('checklists/templates/{template_id}', 'TemplateController@delete');
    $router->post('checklists/templates/{template_id}/assigns', 'TemplateController@assign');

    $router->get('checklists/items', 'CheckListItemController@index');
    $router->post('checklists/complete', 'CheckListItemController@complete');
    $router->post('checklists/incomplete', 'CheckListItemController@incomplete');
    $router->get('checklists/{checklist_id}/items', 'CheckListItemController@items');
    $router->post('checklists/{checklist_id}/items', 'CheckListItemController@store');
    $router->get('checklists/{checklist_id}/items/{checklist_item_id}', 'CheckListItemController@show');
    $router->patch('checklists/{checklist_id}/items/{checklist_item_id}', 'CheckListItemController@update');

    $router->get('checklists', 'CheckListController@index');
    $router->post('checklists', 'CheckListController@store');
    $router->get('checklists/{checklist_id}', ['as' => 'api.checklist.show', 'uses' => 'CheckListController@show']);
    $router->patch('checklists/{checklist_id}', 'CheckListController@update');
    $router->delete('checklists/{checklist_id}', 'CheckListController@delete');

});
