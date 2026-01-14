<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Thing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user',
                'token',
                'token_type'
            ]);
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'token',
                'token_type'
            ]);
    }

    /** @test */
    public function can_view_public_things()
    {
        Thing::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/things');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta'
            ]);
    }

    /** @test */
    public function authenticated_user_can_create_thing()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/things', [
            'name' => 'Test Thing',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Вещь успешно создана!'
            ]);
    }

    /** @test */
    public function cannot_create_thing_without_auth()
    {
        $response = $this->postJson('/api/v1/things', [
            'name' => 'Test Thing',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(401);
    }
}