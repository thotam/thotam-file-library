<?php

use Illuminate\Support\Facades\Route;
use Thotam\ThotamFileLibrary\Http\Controllers\FileLibraryController;
use Thotam\ThotamFileLibrary\Http\Controllers\FilePondUploadController;

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

Route::middleware(['web'])->group(function () {

    Route::redirect('filelibrary', '/', 301);
    Route::group(['prefix' => 'filelibrary'], function () {
        Route::get('{id}/mail_image',  [FileLibraryController::class, 'mail_image'])->name('filelibrary.home_mail_image');
        Route::get('{id}/home_view',  [FileLibraryController::class, 'view'])->name('filelibrary.home_view');
    });
});
