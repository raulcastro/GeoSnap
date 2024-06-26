<?php

use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

/* Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); */

Route::post('/photos/upload', [PhotoController::class, 'upload'])->name('photos.upload');


Route::get('/home', [PhotoController::class, 'index'])->name('home');
Route::get('/photo-list', [PhotoController::class, 'allPhotos']);

Route::get('/last-photo', [PhotoController::class, 'lastPhoto']);
