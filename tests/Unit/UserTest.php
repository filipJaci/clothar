<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DateTime;

use App\Models\User;

class UserTest extends TestCase
{

  use RefreshDatabase;

  /** @test */
  public function a_user_can_be_added(){

    // create a user
    User::factory()->create();

    // see if the number of users in the DB is 1
    $this->assertCount(1, User::all());
  }

  /** @test */
  public function a_user_can_be_edited(){

    // create a user
    User::factory()->create();

    // get a user
    $user = User::first();

    $newDate = date('Y-m-d', strtotime('+1 year'));

    // edit user
    $user->name = 'John Doe';
    $user->email = 'john.doe@mail.com';
    $user->email_verified_at = $newDate;
    $user->password = 'password';
    $user->created_at = $newDate;
    $user->updated_at = $newDate;

    // save changes
    $user->save();

    // get user again
    $user = User::first();

    // validate changes
    $this->assertEquals('John Doe', $user->name);
    $this->assertEquals('john.doe@mail.com', $user->email);
    $this->assertEquals(new DateTime($newDate), $user->email_verified_at);
    $this->assertEquals('password', $user->password);
    $this->assertEquals(new DateTime($newDate), $user->created_at);
    $this->assertEquals(new DateTime($newDate), $user->updated_at);
  }

  /** @test */
  public function a_user_can_be_deleted(){

    // create a user
    User::factory()->create();

    // get a user
    $user = User::first();

    // delete a user
    $user->delete();

    $this->assertCount(0, User::all());
  }
}
