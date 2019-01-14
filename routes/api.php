<?php

use Illuminate\Http\Request;

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
Route::post('register', 'Api\RegisterController@register');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('posts', 'Api\PostController', [ 'as' => 'api' ]);
Route::apiResource('posts.comments', 'Api\Post\CommentController', [ 'as' => 'api' ]);
Route::apiResource('users', 'Api\UserController', [ 'as' => 'api' ]);
Route::apiResource('users.posts', 'Api\User\PostController', [ 'as' => 'api' ]);

