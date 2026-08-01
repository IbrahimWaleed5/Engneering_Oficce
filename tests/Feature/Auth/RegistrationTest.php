<?php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('new users can register', function () {
    Storage::fake('public');

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',

        'country_code' => 'PS',
        'dial_code' => '+970',
        'phone' => '599123456',

        'profile_photo' => UploadedFile::fake()->image(
            'profile.jpg',
            300,
            300
        ),

        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
    'email' => 'test@example.com',
    'phone' => '+970599123456',
]);

    $this->assertAuthenticated();

    $response->assertRedirect(
    route('verification.notice', absolute: false)
);
});
