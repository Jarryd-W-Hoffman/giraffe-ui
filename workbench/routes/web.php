<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\CatalogController;
use Workbench\App\Http\Controllers\OnboardingController;

Route::prefix('catalog')->middleware('web')->group(function () {
    Route::redirect('/', '/catalog/introduction');
    Route::get('assets/catalog.css', [CatalogController::class, 'css']);
    Route::get('assets/alpine.js', [CatalogController::class, 'alpine']);
    Route::get('api/timezones', [CatalogController::class, 'timezones']);
    Route::get('preview/{component}', [CatalogController::class, 'preview']);
    // The Onboarding example has its own controller: each step is a real form post.
    Route::get('examples/onboarding', [OnboardingController::class, 'show']);
    Route::post('examples/onboarding', [OnboardingController::class, 'store']);
    Route::get('examples/{example}', [CatalogController::class, 'example']);
    Route::post('examples/sign-in', [CatalogController::class, 'signIn']);
    Route::post('examples/register', [CatalogController::class, 'register']);
    Route::post('examples/forgot-password', [CatalogController::class, 'forgotPassword']);
    Route::get('{page}', [CatalogController::class, 'show']);
});
