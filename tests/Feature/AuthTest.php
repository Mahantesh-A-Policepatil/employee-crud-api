<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{

    public function test_user_can_register(): void
    {
        $email = 'jane-' . uniqid() . '@example.com';

        $response = $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', $email);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Jane Doe',
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $email = 'john-' . uniqid() . '@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', $user->email);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('email', $user->email);
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $updatedEmail = 'updated-' . uniqid() . '@example.com';
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original-' . uniqid() . '@example.com',
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/user', [
                'name' => 'Updated Name',
                'email' => $updatedEmail,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'User information updated successfully.')
            ->assertJsonPath('user.name', 'Updated Name')
            ->assertJsonPath('user.email', $updatedEmail);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => $updatedEmail,
        ]);
    }

    public function test_authenticated_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/user', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_user_update_rejects_email_used_by_another_user(): void
    {
        $takenEmail = 'taken-' . uniqid() . '@example.com';
        $user = User::factory()->create([
            'email' => 'current-' . uniqid() . '@example.com',
        ]);
        User::factory()->create([
            'email' => $takenEmail,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/user', [
                'name' => $user->name,
                'email' => $takenEmail,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->tokens);
    }
}
