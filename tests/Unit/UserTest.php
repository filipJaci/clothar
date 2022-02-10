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
    // Create a User.
    User::factory()->create();
    // There is 1 User in the DB.
    $this->assertCount(1, User::all());
  }

  /** @test */
  public function a_user_can_be_edited(){
    // Create a User and store the response.
    $user = User::factory()->create();
    // Create a timestamp 1 year into the future.
    $newDate = date('Y-m-d', strtotime('+1 year'));

    // Edit the user.
    $user->name = 'John Doe';
    $user->email = 'john.doe@mail.com';
    $user->email_verified_at = $newDate;
    $user->password = 'password';
    $user->created_at = $newDate;
    $user->updated_at = $newDate;

    // Save changes made to the User.
    $user->save();

    // Get user again.
    $user = User::first();

    // Validate changes.
    $this->assertEquals('John Doe', $user->name);
    $this->assertEquals('john.doe@mail.com', $user->email);
    $this->assertEquals(new DateTime($newDate), $user->email_verified_at);
    $this->assertEquals('password', $user->password);
    $this->assertEquals(new DateTime($newDate), $user->created_at);
    $this->assertEquals(new DateTime($newDate), $user->updated_at);
  }

  /** @test */
  public function a_user_can_be_deleted(){
    // Create a User and store the response.
    $user = User::factory()->create();

    // Delete the created User.
    $user->delete();
    // There are no Users in the DB.
    $this->assertCount(0, User::all());
  }
}