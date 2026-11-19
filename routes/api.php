<?php

use App\Http\Controllers\Api\DeviceTelemetryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/devices')->group(function () {
    Route::post('/telemetry', [DeviceTelemetryController::class, 'ingest'])->name('api.devices.telemetry');
    Route::post('/commands/ack', [DeviceTelemetryController::class, 'acknowledgeCommand'])->name('api.devices.commands.ack');
});
