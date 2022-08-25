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
    // get all users that don't belong to a specific group
    Route::post('user/all-that-dont-belong-to-group', [\App\Http\Controllers\UserController::class, 'getAllThatDontBelongToGroup'])->name('users-that-dont-belong-to-group');
    // get all groups for an user
    Route::get('/user/groups', [\App\Http\Controllers\GroupController::class, 'getAllForUser'])->name('user-groups');
    // get all groups
    Route::get('/groups', [\App\Http\Controllers\GroupController::class, 'show'])->name('get-groups');
    // create group
    Route::post('/create-group', [\App\Http\Controllers\GroupController::class, 'add'])->name('create-group');
    // send notification to members that you want to add to a group
    Route::post('/groups/add-members-notification', [\App\Http\Controllers\GroupController::class, 'addMembersNotification'])->name('group-add-members-notification');
    // add members to a group
    Route::post('groups/add-members', [\App\Http\Controllers\GroupController::class, 'addMembers'])->name('group-add-members');
    // give administrator privileges to a member of a group
    Route::post('/groups/make-administrator', [\App\Http\Controllers\GroupController::class, 'makeAdministrator'])->name('make-administrator');
    // get all notifications by user
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'getAllByUser'])->name('notifications');
    // delete notification
    Route::post('notifications/delete', [\App\Http\Controllers\NotificationController::class, 'remove'])->name('delete-notification');
    // get all recipes
    Route::post('recipes', [\App\Http\Controllers\RecipeController::class, 'show'])->name('get-recipes');
    // get all recipe-steps for a given recipe
    Route::post('recipe-steps' , [\App\Http\Controllers\RecipeStepController::class, 'show'])->name('get-recipe-steps');
    // get all products in stock
    Route::get('product-stock', [\App\Http\Controllers\ProductStockController::class, 'show'])->name('get-product-stock');
    // add products to stock (create product as well if it does not already exist)
    Route::post('add-product-stock', [\App\Http\Controllers\ProductStockController::class, 'create'])->name('create-product-stock');
    // get all products filtered by text input
    Route::post('products', [\App\Http\Controllers\ProductController::class, 'show'])->name('get-products');
});

// create user
Route::post('/register', [\App\Http\Controllers\UserController::class, 'register'])->name('register');
// login user by creating a token
Route::post('/login', [\App\Http\Controllers\UserController::class, 'login'])->name('login');
