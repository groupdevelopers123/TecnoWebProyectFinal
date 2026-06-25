<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PagoFacilCallbackController;

Route::post('/pagofacil/callback', PagoFacilCallbackController::class)
    ->name('api.pagofacil.callback');