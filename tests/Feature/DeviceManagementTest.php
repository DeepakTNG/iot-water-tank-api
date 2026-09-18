<?php

use App\Models\Device;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

test('guests are redirected to the login screen', function () {
    $this->get('/devices')->assertRedirect('/login');
});

test('an operator can sign in and create a device token once', function () {
    $user = User::factory()->create(['password' => 'correct-password']);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertRedirect('/devices');

    $response = $this->post('/devices', ['name' => 'tank-1']);

    $response->assertRedirect('/devices');
    $this->assertDatabaseHas('devices', [
        'user_id' => $user->id,
        'name' => 'tank-1',
        'is_active' => true,
    ]);

    $device = Device::query()->where('name', 'tank-1')->firstOrFail();
    $token = PersonalAccessToken::query()->findOrFail($device->personal_access_token_id);

    expect($token->abilities)->toContain('device:write');
    expect($token->token)->not->toContain('1|');
    expect($response->getSession()->get('device_token'))->toStartWith('1|');
});

test('an operator can revoke only their own device', function () {
    $user = User::factory()->create();
    $device = Device::factory()->for($user)->create();

    $this->actingAs($user)->delete(route('devices.destroy', $device))
        ->assertRedirect();

    expect($device->fresh()->is_active)->toBeFalse();
});

test('operators cannot revoke another users device', function () {
    $device = Device::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('devices.destroy', $device))
        ->assertNotFound();
});
