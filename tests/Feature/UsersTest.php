<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersTest extends TestCase
{
    use WithFaker;

    // public function test_url_check()
    // {
    //     $response = $this->post('/addUser');
    //     $response->assertStatus(200);
    // }

    /**
     * @test
     */
    public function add_new_user()
    {

        // $this->withExceptionHandling();

        $attributes = [
            'username' => $this->faker->userName,
            'password' => md5($this->faker->password),
            'email' => $this->faker->email,
            'contact' => $this->faker->e164PhoneNumber
        ];

        $this->post("/adduser", $attributes);

        $this->assertDatabaseHas('users', $attributes);
    }
}