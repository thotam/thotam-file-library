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

Route::middleware(['web', 'auth', 'CheckAccount', 'CheckHr', 'CheckInfo'])->group(function () {

    //Route FileLibrary
    Route::redirect('filelibrary', '/', 301);
    Route::group(['prefix' => 'filelibrary'], function () {

        Route::get('{id}/view',  [FileLibraryController::class, 'view'])->name('filelibrary.view');
        Route::get('{id}/embed',  [FileLibraryController::class, 'embed'])->name('filelibrary.embed');
        Route::get('{id}/googleapis',  [FileLibraryController::class, 'googleapis'])->name('filelibrary.googleapis');
        Route::get('{id}/image',  [FileLibraryController::class, 'image'])->name('filelibrary.image');
        Route::get('{id}/download',  [FileLibraryController::class, 'download'])->name('filelibrary.download');
        Route::get('{id}/stream',  [FileLibraryController::class, 'stream'])->name('filelibrary.stream');
        Route::get('{id}/video',  [FileLibraryController::class, 'video'])->name('filelibrary.video');
        Route::get('{id}/thumbnail',  [FileLibraryController::class, 'thumbnail'])->name('filelibrary.thumbnail');

        Route::group(['prefix' => 'upload'], function () {
            Route::post('/',  [FilePondUploadController::class, 'create_upload'])->name('filepond.upload');
            Route::patch('/',  [FilePondUploadController::class, 'patch_upload']);
            Route::delete('/',  [FilePondUploadController::class, 'delete_upload']);
        });

        Route::group(['prefix' => 'ckeditor'], function () {
            Route::post('/',  [FilePondUploadController::class, 'ckeditor_upload'])->name('ckeditor.upload');
        });

    });


});

Route::redirect('filelibrary', '/', 301);
Route::group(['prefix' => 'filelibrary'], function () {
    Route::get('{id}/mail_image',  [FileLibraryController::class, 'mail_image'])->name('filelibrary.mail_image');
});
