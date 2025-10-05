<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\TagController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/screenshots', [ScreenshotController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);
Route::post('/screenshots', [ScreenshotController::class, 'store']);
