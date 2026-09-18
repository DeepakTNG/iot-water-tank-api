<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DeviceController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $devices = $request->user()->devices()
            ->with('latestMessage')
            ->latest()
            ->get()
            ->map(fn (Device $device): array => [
                'id' => $device->id,
                'name' => $device->name,
                'is_active' => $device->is_active,
                'created_at' => $device->created_at?->toISOString(),
                'latest_message' => $device->latestMessage?->only(['payload', 'received_at']),
            ]);

        return Inertia::render('Devices/Index', [
            'devices' => $devices,
            'deviceToken' => fn (): ?string => session('device_token'),
        ]);
    }

    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        $token = DB::transaction(function () use ($request): string {
            $device = $request->user()->devices()->create([
                'name' => $request->validated('name'),
            ]);
            $newToken = $request->user()->createToken($device->name, ['device:write', 'device:read']);
            $device->update(['personal_access_token_id' => $newToken->accessToken->id]);

            return $newToken->plainTextToken;
        });

        return redirect()->route('devices.index')->with('device_token', $token);
    }

    public function destroy(Request $request, Device $device): RedirectResponse
    {
        abort_unless($device->user_id === $request->user()->id, 404);

        $device->personalAccessToken?->delete();
        $device->update(['is_active' => false, 'personal_access_token_id' => null]);

        return back();
    }
}
