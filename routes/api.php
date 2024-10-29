<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helper\Routes\RouteHelper;



Route::get('/r', function (Request $request) {
    return 2;
});

// Include all PHP files in the v1 folder
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    RouteHelper::getRoutes(__DIR__ . '/../routes/v1');
 });