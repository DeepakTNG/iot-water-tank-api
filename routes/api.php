<?php

use App\Http\Controllers\Api\DeviceMessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'ability:device:write', 'throttle:device-ingest'])
    ->post('/device-messages', [DeviceMessageController::class, 'store'])
    ->name('device-messages.store');

Route::middleware(['auth:sanctum', 'ability:device:read', 'throttle:device-read'])
    ->get('/devices/{deviceName}/latest', [DeviceMessageController::class, 'latest'])
    ->name('device-messages.latest');
