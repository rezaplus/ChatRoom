<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(\Database\Seeders\RolesTableSeeder::class);
    }

    #[Test]
    public function it_can_register_a_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_can_login_a_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    #[Test]
    public function it_fails_to_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized'
            ]);
    }

    #[Test]
    public function it_can_logout_a_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
    }

//     #[Test]
    // public function admin_can_access_admin_routes()
    // {
    //     $admin = User::factory()->create([
    //         'email' => 'admin@example.com',
    //         'password' => Hash::make('password'),
    //     ]);
    //     $admin->assignRole('Admin');

    //     $token = auth()->login($admin);

    //     $response = $this->withHeaders([
    //         'Authorization' => "Bearer $token",
    //     ])->postJson('/api/chat-rooms', ['name' => 'New Chat Room']);

    //     $response->assertStatus(201);
    // }

    // #[Test]
    // public function user_cannot_access_admin_routes()
    // {
    //     $user = User::factory()->create([
    //         'email' => 'user@example.com',
    //         'password' => Hash::make('password'),
    //     ]);
    //     $user->assignRole('User');

    //     $token = auth()->login($user);

    //     $response = $this->withHeaders([
    //         'Authorization' => "Bearer $token",
    //     ])->postJson('/api/chat-rooms', ['name' => 'New Chat Room']);

    //     $response->assertStatus(403);
    // }

    // #[Test]
    // public function user_can_access_user_routes()
    // {
    //     $user = User::factory()->create([
    //         'email' => 'user@example.com',
    //         'password' => Hash::make('password'),
    //     ]);
    //     $user->assignRole('User');

    //     $token = auth()->login($user);

    //     $response = $this->withHeaders([
    //         'Authorization' => "Bearer $token",
    //     ])->postJson('/api/chat-rooms/1/messages', ['message' => 'Hello World']);

    //     $response->assertStatus(201);
    // }

    // #[Test]
    // public function guest_can_access_guest_routes()
    // {
    //     $guest = User::factory()->create([
    //         'email' => 'guest@example.com',
    //         'password' => Hash::make('password'),
    //     ]);
    //     $guest->assignRole('Guest');

    //     $token = auth()->login($guest);

    //     $response = $this->withHeaders([
    //         'Authorization' => "Bearer $token",
    //     ])->getJson('/api/chat-rooms');

    //     $response->assertStatus(200);
    // }

    // #[Test]
    // public function guest_cannot_access_admin_routes()
    // {
    //     $guest = User::factory()->create([
    //         'email' => 'guest@example.com',
    //         'password' => Hash::make('password'),
    //     ]);
    //     $guest->assignRole('Guest');

    //     $token = auth()->login($guest);

    //     $response = $this->withHeaders([
    //         'Authorization' => "Bearer $token",
    //     ])->postJson('/api/chat-rooms', ['name' => 'New Chat Room']);

    //     $response->assertStatus(403);
    // }
}
