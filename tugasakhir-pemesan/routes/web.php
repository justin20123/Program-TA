<?php

use App\Http\Controllers\RatingController;
use App\Http\Controllers\VendorController;
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

Route::get('/', [VendorController::class, 'index'])->name('home');
Route::post('/location', [VendorController::class, 'load']);
Route::get('/vendor/rating', [VendorController::class, 'getRating']);
