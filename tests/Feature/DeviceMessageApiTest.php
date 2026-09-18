<?php

use App\Models\Device;
use App\Models\DeviceMessage;
use App\Models\User;

function createDeviceToken(User $user, string $name, array $abilities): array
{
    $device = Device::factory()->for($user)->create(['name' => $name]);
    $newToken = $user->createToken($name, $abilities);
    $device->update(['personal_access_token_id' => $newToken->accessToken->id]);

    return [$device, $newToken->plainTextToken];
}

test('a device can submit a JSON reading', function () {
    $user = User::factory()->create();
    [$device, $token] = createDeviceToken($user, 'tank-1', ['device:write']);

    $response = $this->withToken($token)->postJson('/api/device-messages', [
        'device_name' => $device->name,
        'payload' => ['level' => 72.5, 'pump_on' => false],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.device_name', 'tank-1')
        ->assertJsonPath('data.payload.level', 72.5);

    expect(DeviceMessage::query()->count())->toBe(1);
    expect(DeviceMessage::query()->first()->received_at)->not->toBeNull();
});

test('submitting a reading requires authentication and the write ability', function () {
    $this->postJson('/api/device-messages', [
        'device_name' => 'tank-1',
        'payload' => ['level' => 72.5],
    ])->assertUnauthorized();

    [$device, $token] = createDeviceToken(User::factory()->create(), 'tank-1', ['device:read']);

    $this->withToken($token)->postJson('/api/device-messages', [
        'device_name' => $device->name,
        'payload' => ['level' => 72.5],
    ])->assertForbidden();
});

test('a reading validates its device name and JSON object payload', function () {
    [$device, $token] = createDeviceToken(User::factory()->create(), 'tank-1', ['device:write']);

    $this->withToken($token)->postJson('/api/device-messages', [
        'device_name' => '',
        'payload' => ['level', 'invalid-object'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['device_name', 'payload']);
});

test('the latest reading is authenticated, ordered, and isolated by device', function () {
    $user = User::factory()->create();
    [$device, $token] = createDeviceToken($user, 'tank-1', ['device:read']);
    $otherDevice = Device::factory()->for($user)->create(['name' => 'tank-2']);

    DeviceMessage::factory()->create([
        'device_id' => $device->id,
        'device_name' => $device->name,
        'payload' => ['level' => 40],
        'received_at' => now()->subMinute(),
    ]);
    DeviceMessage::factory()->create([
        'device_id' => $device->id,
        'device_name' => $device->name,
        'payload' => ['level' => 80],
        'received_at' => now(),
    ]);
    DeviceMessage::factory()->create([
        'device_id' => $otherDevice->id,
        'device_name' => $otherDevice->name,
        'payload' => ['level' => 99],
        'received_at' => now()->addMinute(),
    ]);

    $this->withToken($token)->getJson('/api/devices/tank-1/latest')
        ->assertOk()
        ->assertJsonPath('data.payload.level', 80);
});

test('latest reading requires authentication', function () {
    $this->getJson('/api/devices/tank-1/latest')
        ->assertUnauthorized();
});

test('latest reading returns not found when the device has no messages', function () {
    [$device, $token] = createDeviceToken(User::factory()->create(), 'tank-1', ['device:read']);

    $this->withToken($token)->getJson('/api/devices/unknown/latest')
        ->assertNotFound();
});
