<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    /**
     * Test the AuthController@signUp method.
     */
    public function testCanSignUpWithValidData(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson(route('signUp'), $userData);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Test the AuthController@signUp method with invalid data.
     */
    public function testCannotSignUpWithInvalidData(): void
    {
        $userData = [
            'name' => '',
            'email' => 'testuser',
            'password' => 'password',
            'password_confirmation' => 'pass123',
        ];

        $response = $this->postJson(route('signUp'), $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test the AuthController@signIn method with valid credentials.
     */
    public function testCanSignInWithValidCredentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $credentials = [
            'email' => $user->email,
            'password' => 'password123'
        ];

        $response = $this->postJson(route('signIn'), $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    /**
     * Test the AuthController@signIn method with invalid credentials.
     */
    public function testCannotSignInWithInvalidCredentials(): void
    {
        User::factory()->create(['password' => bcrypt('password123')]);

        $credentials = [
            'email' => 'invaliduser@test.com',
            'password' => 'invalidpassword'
        ];

        $response = $this->postJson(route('signIn'), $credentials);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid credentials']);
    }
}
