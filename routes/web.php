<?php

use Illuminate\Support\Facades\Route;
use Keyvanarasteh\LaravelBones\Http\Controllers\BonesController;

Route::prefix(config('bones.route_prefix'))
    ->middleware(config('bones.middleware'))
    ->name('bones.')
    ->group(function () {
        Route::get('/', [BonesController::class, 'index'])->name('dashboard');
        Route::get('/data', [BonesController::class, 'data'])->name('data');
        Route::post('/refresh', [BonesController::class, 'refresh'])->name('refresh');
    });
