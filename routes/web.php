<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('auth/token_details', 'AuthController@tokenDetails');

    $router->delete('session/{id}', 'TeachingSessionController@delete');
    $router->delete('session/{sessionId}/book/{bookId}', 'TeachingSessionController@removeBook');
    $router->get('sessions', 'TeachingSessionController@getList');
    $router->get('session/{id}', 'TeachingSessionController@getDetails');
    $router->post('session', 'TeachingSessionController@createNew');
    $router->put('session/{sessionId}/book/{bookId}', 'TeachingSessionController@assignBook');
});