<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('privacy policy page is accessible by guests and renders PrivacyPolicy component', function () {
    $this->get('/privacy-policy')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('PrivacyPolicy'));
});

test('terms of service page is accessible by guests and renders TermsOfService component', function () {
    $this->get('/terms-of-service')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('TermsOfService'));
});

test('privacy shortcut redirects to privacy-policy', function () {
    $this->get('/privacy')->assertRedirect('/privacy-policy');
});

test('terms shortcut redirects to terms-of-service', function () {
    $this->get('/terms')->assertRedirect('/terms-of-service');
});

test('legal pages are accessible by authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/privacy-policy')->assertOk();
    $this->actingAs($user)->get('/terms-of-service')->assertOk();
});
