<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Users as Usr;

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

        $user = new \App\Models\Users();
        $rq   = new  \Illuminate\Http\Request();

        $rq->username = $this->faker->userName;
        $rq->password = md5($this->faker->password);
        $rq->email    = $this->faker->email;
        $rq->contact  = $this->faker->e164PhoneNumber;

        $u = new Usr();

        $output = $u->add($rq);

        $this->assertEquals($output->original['code'], 200);
    }
}