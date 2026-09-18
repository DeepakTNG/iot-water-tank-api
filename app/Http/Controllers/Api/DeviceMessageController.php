<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceMessageRequest;
use App\Models\Device;
use App\Models\DeviceMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class DeviceMessageController extends Controller
{
    public function store(StoreDeviceMessageRequest $request): JsonResponse
    {
        $device = $this->authenticatedDevice($request);

        abort_unless($request->validated('device_name') === $device->name, Response::HTTP_UNPROCESSABLE_ENTITY, 'The device name does not match the token.');

        $message = DeviceMessage::create([
            ...$request->validated(),
            'device_id' => $device->id,
            'received_at' => Carbon::now(),
        ]);

        return response()->json(['data' => $message], Response::HTTP_CREATED);
    }

    public function latest(string $deviceName): JsonResponse
    {
        $device = $this->authenticatedDevice(request());

        abort_unless($device->name === $deviceName, Response::HTTP_NOT_FOUND);

        $message = DeviceMessage::query()
            ->where('device_id', $device->id)
            ->latest('received_at')
            ->latest('id')
            ->first();

        abort_if($message === null, Response::HTTP_NOT_FOUND, 'No reading found for this device.');

        return response()->json(['data' => $message]);
    }

    private function authenticatedDevice(Request $request): Device
    {
        $tokenId = $request->user()?->currentAccessToken()?->getKey();
        $device = Device::query()
            ->where('personal_access_token_id', $tokenId)
            ->where('is_active', true)
            ->first();

        abort_unless($device !== null, Response::HTTP_UNAUTHORIZED, 'This token is not linked to an active device.');

        return $device;
    }
}
