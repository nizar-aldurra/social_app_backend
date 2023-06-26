<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller(AuthenticationController::class)->prefix('auth')->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout')->middleware('auth:api');
});

Route::controller(UserController::class)->middleware('auth:api')->prefix('user')->group(function () {
    Route::get('users', 'allUsers');
    Route::get('posts', 'getPosts');
    Route::get('liked_posts', 'getLikedPosts');
    Route::get('comments', 'getComments');
    Route::get('info', 'getUserInfo');
    Route::get('info/{user}', 'getUserInfoAndPostsById');
    Route::get('posts/{user}', 'getUserPostsById');
    Route::post('update_info', 'updateInfo');
    Route::post('update_password', 'updatePassword');
    Route::post('update/{user}', 'updateUserById');
    Route::delete('{user}', 'deleteUserById');
});
Route::controller(PostController::class)->middleware('auth:api')->prefix('post')->group(function () {
    Route::get('all', 'all');
    Route::get('comments', 'getPostComments');
    Route::get('{post}/owner', 'getOwner');
    Route::get('{post}/comments', 'getComments');
    Route::get('{post}', 'get');
    Route::post('create', 'create');
    Route::get('{post}/change_liking_status', 'changeLikingStatus');
    Route::post('update/{post}', 'update');
    Route::delete('{post}', 'delete');
});

Route::controller(CommentController::class)->middleware('auth:api')->prefix('comment')->group(function () {
    Route::get('all', 'all');
    Route::get('{comment}/owner', 'getOwner');
    Route::get('{comment}/post', 'getPost');
    Route::get('{comment}', 'get');
    Route::post('create', 'create');
    Route::post('update/{comment}', 'update');
    Route::delete('{comment}', 'delete');
});