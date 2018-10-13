<?php

use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;

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

Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');
Route::post('email/resend', 'Auth\VerificationController@resend');
Route::get('email/verify', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('logout', 'Auth\LoginController@logout');
Route::post('forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('reset-password', 'Auth\ResetPasswordController@reset')->name('password.reset');

Route::middleware('auth:api')->group(function () {
    Route::get('/me', function (Request $request) {
        return new UserResource(auth()->user());
    });
});
