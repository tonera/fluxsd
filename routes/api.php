<?php

use App\Http\Controllers\api\ApiJobController;
use App\Http\Controllers\api\ConfigApiController;
use App\Http\Controllers\api\ModelController;
use App\Http\Controllers\api\PartJobController;
use App\Http\Controllers\api\SetupController;
use App\Http\Controllers\api\SSEController;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\TaskImagesController;
use App\Http\Controllers\api\TextController;
use App\Http\Controllers\api\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::post('/task/upload', [ TaskController::class , 'upload'])->name('task.upload');
    Route::post('/config/update', [ConfigApiController::class, 'update']);
    Route::post('/config/active', [ConfigApiController::class, 'active']);
    Route::post('/models/download', [ModelController::class , 'download']);
    Route::delete('/models/{amModel}', [ModelController::class , 'destroy']);
    // Route::get('/download/percent', [SSEController::class , 'downloadPercent']);//sse，负载太高，废弃
    Route::get('/download/progress', [SSEController::class , 'downloadProgress']);

    Route::post('/partjob/toggle', [PartJobController::class , 'toggle']);
    Route::get('/partjob/status', [PartJobController::class , 'status']);
    Route::get('/partjob/deposits', [PartJobController::class , 'deposits']);

    Route::get('/works', [ TaskImagesController::class , 'works']);
    Route::post('/image/convert', [ TaskImagesController::class , 'convert']);

    Route::post('/text/ask', [TextController::class , 'ask']);

    Route::post('/video', [VideoController::class , 'store']);
    Route::post('/task', [ TaskController::class , 'store'])->name('task.store');
    Route::post('/task/upscale', [TaskController::class ,'upscale']);
    // Route::resource('models', ModelController::class);
    Route::resource('images', TaskImagesController::class);
    // Route::resource('models', ModelController::class);
    Route::post('/models/dlstatus', [ModelController::class , 'downloadStatus']);
    
    Route::put('/models/{model}', [ModelController::class , 'update']);

    Route::get('/text/models', [TextController::class , 'models']);
    

});
Route::get('/models', [ModelController::class , 'index']);
Route::post('/job/checktoken', [ApiJobController::class, 'checkToken']);
Route::post('/job/callback', [ ApiJobController::class , 'callback'])->name('job.callback');

Route::post('/setup', [SetupController::class, 'store']);

