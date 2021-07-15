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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('admin')->namespace('Admin')->group(function(){
    Route::match(['get','post'], '/login', 'AdminController@login');
    Route::post('/forgot-password', 'AdminController@forgotPassword');
    Route::post('/reset-password/{token}', 'AdminController@resetPassword');

    Route::group(['middleware'=>['auth:admin']], function(){
       
       //User Route
       Route::get('/all-user/{page}', 'UserController@allUser');
       Route::post('/create-user', 'UserController@createUser');
       Route::get('/edit-user/{id}', 'UserController@editUser');
       Route::post('/update-user/{id}', 'UserController@updateUser');
       Route::post('/update-user-password/{id}', 'UserController@updateUserPassword');
       Route::get('/delete-user/{id}', 'UserController@deleteUser');
       
    });
});
