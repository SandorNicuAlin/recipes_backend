<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function() {
    // get current logged in user
    Route::get('/user', [\App\Http\Controllers\UserController::class, 'show'])->name('get-user');
    // logout the user
    Route::get('/logout', [\App\Http\Controllers\UserController::class, 'logout'])->name('logout');
    // edit one field of a user by passing the field dynamically
    Route::post('/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('edit-user');
    // get all groups from an user
    Route::get('/user/groups', [\App\Http\Controllers\GroupController::class, 'getAllForUser'])->name('user-groups');
});

// create user
Route::post('/register', [\App\Http\Controllers\UserController::class, 'register'])->name('register');
// login user by creating a token
Route::post('/login', [\App\Http\Controllers\UserController::class, 'login'])->name('login');
