<?php

use App\Models\Device;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

test('guests are redirected to the login screen', function () {
    $this->get('/devices')->assertRedirect('/login');
});

test('password login is disabled', function () {
    $this->post('/login', [
        'email' => 'operator@gmail.com',
        'password' => 'anything',
    ])->assertMethodNotAllowed();
});

test('an approved verified Google account can sign in and create a device token once', function () {
    $user = User::factory()->create(['email' => 'operator@gmail.com']);
    Socialite::fake('google', GoogleUser::fake([
        'id' => 'google-sub-1',
        'email' => $user->email,
        'email_verified' => true,
    ]));

    $this->get('/auth/google/callback')->assertRedirect('/devices');

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
    expect($user->fresh()->google_id)->toBe('google-sub-1');
});

test('Google sign in rejects unapproved or unverified accounts', function () {
    $user = User::factory()->create(['email' => 'operator@gmail.com']);

    Socialite::fake('google', GoogleUser::fake([
        'id' => 'google-sub-unverified',
        'email' => $user->email,
        'email_verified' => false,
    ]));

    $this->get('/auth/google/callback')
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');

    Socialite::fake('google', GoogleUser::fake([
        'id' => 'google-sub-unapproved',
        'email' => 'other@gmail.com',
        'email_verified' => true,
    ]));

    $this->get('/auth/google/callback')
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');
});

test('Google sign in cannot rebind an existing Google identity', function () {
    $firstUser = User::factory()->create([
        'email' => 'first@gmail.com',
        'google_id' => 'google-sub-1',
    ]);
    $secondUser = User::factory()->create(['email' => 'second@gmail.com']);
    Socialite::fake('google', GoogleUser::fake([
        'id' => $firstUser->google_id,
        'email' => $secondUser->email,
        'email_verified' => true,
    ]));

    $this->get('/auth/google/callback')
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');
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
