<?php


use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\ProductController;

// Route::middleware('verify.shopify')->group(function () {
//     Route::post('/create-product', [ProductController::class, 'createProduct'])->middleware('verify.shopify');
//    Route::post('/update-product', [ProductController::class, 'update'])->middleware('verify.shopify');

//     Route::get('/get-products', [ProductController::class, 'getProducts'])->middleware('verify.shopify');
// });

use App\Http\Controllers\BundleController;

Route::middleware('verify.shopify')->group(function () {
    Route::get('/bundles', [BundleController::class, 'getBundles']);
    Route::post('/bundle/save', [BundleController::class, 'saveBundle']);
});
Route::delete('/bundle/delete/{id}', [BundleController::class, 'deleteBundle'])
    ->middleware('verify.shopify');
