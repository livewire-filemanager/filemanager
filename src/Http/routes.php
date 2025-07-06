<?php

use Illuminate\Support\Facades\Route;
use LivewireFilemanager\Filemanager\Http\Controllers\Api\BulkUploadController;
use LivewireFilemanager\Filemanager\Http\Controllers\Api\FileController;
use LivewireFilemanager\Filemanager\Http\Controllers\Api\FolderController;

Route::prefix(config('livewire-fileuploader.api.prefix', 'filemanager/v1'))
    ->middleware(config('livewire-fileuploader.api.middleware', ['api']))
    ->name('filemanager.api.')
    ->group(function () {
        Route::apiResource('folders', FolderController::class);
        Route::apiResource('files', FileController::class);
        Route::post('files/bulk', [BulkUploadController::class, 'store'])->name('files.bulk');
        Route::post('folders/{folder}/upload', [FolderController::class, 'upload'])->name('folders.upload');
    });
