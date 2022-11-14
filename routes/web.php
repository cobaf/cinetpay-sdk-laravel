<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\CinetPayController::class, 'index']);
Route::post('/', [\App\Http\Controllers\CinetPayController::class, 'Payment']);
Route::match(['get','post'],'/notify_url', [\App\Http\Controllers\CinetPayController::class, 'notify_url'])->name('notify_url');
Route::match(['get','post'],'/return_url', [\App\Http\Controllers\CinetPayController::class, 'return_url'])->name('return_url');
