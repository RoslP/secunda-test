<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\ActivityController;

Route::middleware('api_key')->group(function () {
    // Organizations
    Route::get('organizations/{id}', [OrganizationController::class, 'show']);
    Route::get('organizations', [OrganizationController::class, 'byBuilding']); // ?building_id=
    Route::get('organizations/by-activity/{activityId}', [OrganizationController::class, 'byActivity']);
    Route::get('organizations/search-by-activity/{activityId}', [OrganizationController::class, 'searchByActivity']);
    Route::get('organizations/search-name', [OrganizationController::class, 'searchByName']); // ?name=
    Route::get('organizations/nearby', [OrganizationController::class, 'nearby']); // ?lat=&lng=&radius_km=
    Route::get('organizations/in-rect', [OrganizationController::class, 'inRectangle']); // min_lat,max_lat,min_lng,max_lng

    // Buildings
    Route::get('buildings', [BuildingController::class, 'index']);
    Route::get('buildings/{id}', [BuildingController::class, 'show']);

    // Activities
    Route::get('activities', [ActivityController::class, 'index']);
    Route::post('activities', [ActivityController::class, 'store']);
});
