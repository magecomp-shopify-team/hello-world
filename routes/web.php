<?php

use App\Http\Controllers\FrontEndControllr;
use Illuminate\Support\Facades\Route;


Route::get('/', [FrontEndControllr::class, 'index'])
    ->middleware('verify.shopify')
    ->name('home');


Route::fallback([FrontEndControllr::class, 'index']);


    use App\Http\Controllers\BundleController;

Route::post('/bundle/save', [BundleController::class, 'saveBundle'])->middleware('verify.shopify');


Route::delete('/bundle/delete/{id}', [BundleController::class, 'deleteBundle'])
    ->middleware('verify.shopify');