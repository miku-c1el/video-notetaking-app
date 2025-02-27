<?php

use App\Providers\RouteServiceProvider;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'username' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('new users cannot register with invalid password (w/o symbol)', function () {
    $response = $this->post('/register', [
        'username' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors(['password' => 'パスワードには特殊文字を含める必要があります。']);
});

test('new users cannot register with invalid password (w/o uppercase)', function () {
    $response = $this->post('/register', [
        'username' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123!',
        'password_confirmation' => 'password123!',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors(['password' => 'パスワード には大文字と小文字の両方を含める必要があります']);
});

test('new users cannot register with invalid password (w/o lowercase)', function () {
    $response = $this->post('/register', [
        'username' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'PASSWORD123!',
        'password_confirmation' => 'PASSWORD123!',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors(['password' => 'パスワード には大文字と小文字の両方を含める必要があります']);
});

test('new users cannot register with invalid password (w/o number)', function () {
    $response = $this->post('/register', [
        'username' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'PASSWORDd!!!!!!',
        'password_confirmation' => 'PASSWORDd!!!!!!',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors(['password' => 'パスワード には数字を含める必要があります。']);
});

test('new users cannot register with wrong confirmation password', function () {
    $response = $this->post('/register', [
        'username' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'PassWord123!',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors(['password' => 'パスワードが確認用と一致しません。']);
});

test('new users cannot register with invalid email', function () {
    $response = $this
        ->post('/register', [
            'username' => 'Test User',
            'email' => 'testexample',
            'password' => 'PassWord123!',
            'password_confirmation' => 'PassWord123!',
        ]);

    $this->assertGuest();

    $response->assertSessionHasErrors('email');
});

test('new users cannot register with existing email', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this
        ->post('/register', [
            'username' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'PassWord123!',
            'password_confirmation' => 'PassWord123!',
        ]);

    $this->assertGuest();

    $response->assertSessionHasErrors('email');
});
