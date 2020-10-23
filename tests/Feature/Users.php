<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Users extends TestCase
{
    use WithFaker;

    // use RefreshDatabase;

    /**@test*/
    // public function test_url_check()
    // {
    //     $response = $this->post('/addUser');
    //     $response->assertStatus(200);
    // }

    /**@test*/
    public function test_add_new_user()
    {

        // $this->withExceptionHandling();

        $attributes = [
            'username' => $this->faker->userName,
            'password' => md5($this->faker->password),
            'email' => $this->faker->email,
            'contact' => $this->faker->e164PhoneNumber
        ];

        $output = $this->post("/adduser", $attributes);
        // var_dump($output);
        // die;
        $this->assertDatabaseHas('users',$attributes);
        // $this->assertEquals($output, "SAVED");
    }
}
