<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Mockery\Mock;
use Tests\TestCase;

class AppLoginTest extends TestCase
{

    private $userObject = null;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testSuccessfulLogin()
    {
        $user = User::factory()->count(1)->create()->first();
        $this->userObject = $user;

        $useremail = $user->email;
        $password = 'password';

        $loginData = ['email' => $useremail, 'password' => $password];

        $response = $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json']);
        $response->assertStatus(200);

        $jsonResponse = $response->json();
        $token = $jsonResponse['data']['token'];

        $this->assertNotEmpty($token);

        $user->delete();
    }
}
