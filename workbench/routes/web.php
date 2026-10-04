<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\CatalogController;

Route::prefix('catalog')->middleware('web')->group(function () {
    Route::redirect('/', '/catalog/introduction');
    Route::get('assets/catalog.css', [CatalogController::class, 'css']);
    Route::get('preview/{component}', [CatalogController::class, 'preview']);
    Route::get('examples/{example}', [CatalogController::class, 'example']);
    Route::post('examples/sign-in', [CatalogController::class, 'signIn']);
    Route::post('examples/register', [CatalogController::class, 'register']);
    Route::post('examples/forgot-password', [CatalogController::class, 'forgotPassword']);
    Route::get('{page}', [CatalogController::class, 'show']);
});
