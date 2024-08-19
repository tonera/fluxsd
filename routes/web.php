<?php

use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\WorksController;

use Illuminate\Support\Facades\Route;

Route::get('/', [ IndexController::class , 'index'])->name('index');
Route::get('/changeLanguage', [LanguageController::class, 'changeLanguage'])->name('changeLanguage');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [ ImageController::class , 'index'])->name('dashboard');
    
    Route::get('/image', [ ImageController::class , 'index'])->name('image.index');

    Route::get('/config', [ ConfigController::class , 'index'])->name('config');
    Route::get('/models', [ IndexController::class , 'models'])->name('models');

    Route::get('/works', [ WorksController::class , 'index'])->name('works.index');
    Route::get('/text', [ IndexController::class , 'text'])->name('text.index');
    Route::get('/video', [ IndexController::class , 'video'])->name('video.index');

    Route::get('/tools/cropper', [ ToolsController::class , 'cropper'])->name('tools.cropper');
    Route::get('/tools/upscale', [ ToolsController::class , 'upscale'])->name('tools.upscale');
    Route::get('/tools/removebg', [ ToolsController::class , 'removebg'])->name('tools.removebg');
    Route::get('/tools/faceswap', [ ToolsController::class , 'faceswap'])->name('tools.faceswap');

});

Route::get('/setup', [ IndexController::class , 'setup'])->name('index.setup');
