<?php

use App\Http\Controllers\UserController;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Api','as' => 'api.'],function () {
    Route::get('/users', function () {
        return (new UserController())->index();
    });

    Route::post('/signup', function (StoreUserRequest $request) {
        return (new UserController())->store($request);
    });

    Route::post('/login', function (LoginUserRequest $request) {
        return (new UserController())->login($request);
    });

    Route::get('/users/{id}', function (string $id) {
            return (new UserController())->show($id);
        });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::put('/users/{id}', function ($id, UpdateUserRequest $request) {
            return (new UserController())->update($request, $id);
        });

        Route::delete('/users/{id}', function ($id) {
            return (new UserController())->destroy($id);
        });
    });
});


Route::get('/', function () {
    return response()->json(['message' => 'Welcome'], 200);
});


