<?php

use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\WorksController;

// use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ IndexController::class , 'index'])->name('index');
Route::get('/changeLanguage', [LanguageController::class, 'changeLanguage'])->name('changeLanguage');
// Route::get('/test', function () {
//     return Inertia::render('Test');
// })->name('test');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [ ImageController::class , 'index'])->name('dashboard');
    
    //functio返回inertia可以正确返回X-Inertia头，[class]形式返回则不行
    Route::get('/image', [ ImageController::class , 'index'])->name('image.index');

    Route::get('/config', [ ConfigController::class , 'index'])->name('config');
    Route::get('/models', [ IndexController::class , 'models'])->name('models');

    //for test reverb
    Route::get('/messages', [MessageController::class, 'messages'])->name('messages');
    Route::post('/message', [MessageController::class, 'message'])->name('message');

    Route::get('/works', [ WorksController::class , 'index'])->name('works.index');
    Route::get('/text', [ IndexController::class , 'text'])->name('text.index');
    Route::get('/video', [ IndexController::class , 'video'])->name('video.index');

    Route::get('/tools/cropper', [ ToolsController::class , 'cropper'])->name('tools.cropper');
    Route::get('/tools/upscale', [ ToolsController::class , 'upscale'])->name('tools.upscale');
    Route::get('/tools/removebg', [ ToolsController::class , 'removebg'])->name('tools.removebg');
    Route::get('/tools/faceswap', [ ToolsController::class , 'faceswap'])->name('tools.faceswap');

});

Route::get('/setup', [ IndexController::class , 'setup'])->name('index.setup');



// Broadcast::routes(['middleware' => ['auth:sanctum']]);
