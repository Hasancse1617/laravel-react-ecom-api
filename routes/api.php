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
       
       //Brand Route
       Route::get('/brands/{page}', 'BrandController@brands');
       Route::post('/create-brand', 'BrandController@createBrand');
       Route::post('/status-brand', 'BrandController@statusBrand');
       Route::get('/edit-brand/{id}', 'BrandController@editBrand');
       Route::post('/update-brand/{id}', 'BrandController@updateBrand');
       Route::get('/delete-brand/{id}', 'BrandController@deleteBrand');

       //Banner Route
       Route::get('/all-banner/{page}', 'BannerController@allBanner');
       Route::post('/create-banner', 'BannerController@createBanner');
       Route::post('/status-banner', 'BannerController@statusBanner');
       Route::get('/edit-banner/{id}', 'BannerController@editBanner');
       Route::post('/update-banner/{id}', 'BannerController@updateBanner');
       Route::get('/delete-banner/{id}', 'BannerController@deleteBanner');

       //Category Route
       Route::get('/all-category/{page}', 'CategoryController@allCategory');
       Route::get('/parent-categories', 'CategoryController@parentCategory');
       Route::post('/create-category', 'CategoryController@createCategory');
       Route::post('/status-category', 'CategoryController@statusCategory');
       Route::get('/edit-category/{id}', 'CategoryController@editCategory');
       Route::post('/update-category/{id}', 'CategoryController@updateCategory');
       Route::get('/delete-category/{id}', 'CategoryController@deleteCategory');

       //Product Route
       Route::get('/all-product/{page}', 'ProductController@allProduct');
       Route::get('/all-categories', 'ProductController@allCategories');
       Route::get('/all-brands', 'ProductController@allBrands');
       Route::post('/create-product', 'ProductController@createProduct');
       Route::post('/status-product', 'ProductController@statusProduct');
       Route::get('/edit-product/{id}', 'ProductController@editProduct');
       Route::post('/update-product/{id}', 'ProductController@updateProduct');
       Route::get('/delete-product/{id}', 'ProductController@deleteProduct');

       //ProductImages
       Route::post('/add-product-images/{id}', 'ProductController@addImages');
       Route::get('/all-images/{id}', 'ProductController@allImages');
       Route::get('/delete-product-image/{id}', 'ProductController@deleteImages');
       Route::post('/status-image', 'ProductController@statusImage');
    
    });
});
