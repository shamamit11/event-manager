<?php

use Illuminate\Support\Facades\Route;
use App\Interfaces\Http\Controllers\EventController;

Route::controller(EventController::class)->group(function () {
    Route::post('/events', 'store');
    Route::put('/events/{id}', 'update');
    Route::get('/events/list', 'list');
    Route::delete('/events/{id}', 'destroy');
});
