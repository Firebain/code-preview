<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Robokassa routes
Route::group(['prefix' => 'robokassa'], function () {
    Route::post('hook', 'RobokassaController@hook');

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/', 'RobokassaController@index');
    });
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/tinkoff', 'TinkoffController@index');
});

// Auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', 'AuthController@logout');
        Route::post('me', 'AuthController@me');

        Route::post('password/change', 'AuthController@change_password');
        Route::post('photo/change', 'AuthController@change_photo');
    });

    Route::get('email/verify/{id}', 'AuthController@emailVerify')->name('verification.verify');
});

// Routes without login

Route::apiResource('contents', 'ContentController')
    ->only(['index']);

Route::apiResource('services', 'ServiceController')
    ->only(['index']);

Route::apiResource('books', 'BookController')
    ->only(['index']);

// Account routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('withdrawals', 'WithdrawalController@withdrawal');

    Route::apiResource('emotions', 'EmotionController')
        ->only(['index', 'store']);

    Route::get('emotions/history', 'EmotionController@history');

    Route::group([
        'middleware' => ['has_subscribtion'],
        'prefix' => 'users'
    ], function () {
        Route::get('followers', 'UserController@followers');

        Route::get('structure', 'UserController@structure');

        Route::get('finances', 'UserController@finances');

        Route::post('finances/send', 'UserController@send');

        Route::get('teaching', 'TeachingController@index');

        Route::get('contracts', 'ContractController@index');
    });

    Route::group(['middleware' => ['subscribed']], function () {
        Route::apiResource('tables', 'TableController')
            ->only(['index', 'store', 'update'])
            ->parameters(['tables' => 'pivot']);

        Route::get('tables/history', 'TableController@history');
    });

    Route::group(['prefix' => 'wheel'], function () {
        Route::get('/', 'WheelOfLifeController@index');

        Route::post('{id}/check', 'WheelOfLifeController@check');

        Route::post('{id}/uncheck', 'WheelOfLifeController@uncheck');

        Route::apiResource('purposes', 'PurposeController')
            ->only(['store', 'destroy']);
    });

    Route::group(['prefix' => 'lessons'], function () {
        Route::get('blocks', 'LessonController@blocks');

        Route::get('stats', 'LessonController@stats');

        Route::get('{id}', 'LessonController@show');

        Route::post('{id}/submit', 'LessonController@submit');
    });

    Route::apiResource('custom_fields', 'CustomFieldController')
            ->only(['index', 'store']);

    Route::get('cheats', 'CheatController@index');

    Route::post('promocode', 'PromocodeController@activate');
});