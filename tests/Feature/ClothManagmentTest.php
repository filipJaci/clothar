<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

use App\Models\Cloth;
use App\Models\User;

class ClothManagmentTest extends TestCase{

  // Creates a user.
  private function createUser(){
    return User::factory()->create();
  }

  // Creates a cloth.
  private function createCloth(){
    // As a user
    return $this->actingAs($this->createUser())
    // create a cloth.
    ->post('/api/clothes',[
      'title' => 'Short Sleeves shirt',
      'description' => null,
      'category' => null,
      'buy_at' => null,
      'buy_date' => null,
      'status' => 1,
    ]);
  }

  // Checks api repsonse format, wheter or not all keys are present.
  private function checkResponseFormat($response){
    $this->assertArrayHasKey('title', $response);
    $this->assertArrayHasKey('message', $response);
    $this->assertArrayHasKey('write', $response);
    $this->assertArrayHasKey('data', $response);
  }

  use RefreshDatabase;

  /** @test */
  public function a_piece_of_cloth_can_be_added(){

    // display more accurate errors
    $this->withoutExceptionHandling();
    // create a Cloth
    $response = $this->createCloth();

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // there is 1 Cloth in the DB
    $this->assertCount(1, Cloth::all());
  }

  /** @test */
  public function only_users_own_clothes_are_displayed(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Clothes via 2 different users.
    $this->createCloth();
    $this->createCloth();

    // Store the response.
    // As a first user
    $response = $this->actingAs(User::first())
    // get all clothes.
    ->get('api/clothes/');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 Cloth fetched from the DB.
    $this->assertCount(1, $response['data']);
  }

  /** @test */
  public function a_piece_of_cloth_can_be_updated(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a piece of Cloth.
    $this->createCloth();

    // As the first user
    $response = $this->actingAs(User::first())
    // update first cloth.
    ->patch('api/clothes/' . Cloth::first()->id, [
      // New values.
      'title' => 'Long Sleeves shirt',
      'description' => "new description",
      'category' => 1,
      'buy_at' => "ACME Store",
      'buy_date' => "2020-10-20",
      'status' => 2,
    ]);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check response format.
    $this->checkResponseFormat($response);

    // get updated Cloth from the DB
    $cloth = Cloth::first();

    // check if Cloth has been updated
    $this->assertEquals('Long Sleeves shirt', $cloth->title);
    $this->assertEquals('new description', $cloth->description);
    $this->assertEquals(1, $cloth->category);
    $this->assertEquals('ACME Store', $cloth->buy_at);
    $this->assertEquals(Carbon::parse('2020-10-20'), $cloth->buy_date);
    $this->assertEquals(2, $cloth->status);

  }

  /** @test */
  public function cloth_update_returns_all_of_users_clothes(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Clothes via 2 different users.
    $this->createCloth();
    $this->createCloth();

    // Get the first user.
    $firstUser = User::first();

    // As the first user
    $this->actingAs($firstUser)
    // create a new cloth.
    ->post('api/clothes/', [
      // New cloth.
      'title' => 'Third shirt',
      'description' => "Third description.",
      'category' => 1,
      'buy_at' => "Third Store",
      'buy_date' => "2020-03-03",
      'status' => 2,
    ]);

    // As the first user
    $response = $this->actingAs(User::first())
    // update first cloth.
    ->patch('api/clothes/' . Cloth::first()->id, [
      // New values.
      'title' => 'Long Sleeves shirt',
      'description' => "new description",
      'category' => 1,
      'buy_at' => "ACME Store",
      'buy_date' => "2020-10-20",
      'status' => 2,
    ]);

    // Response returned 2 clothes items.
    $this->assertCount(2, $response['data']);
    // 2 clothes both belong to the first user.
    $this->assertEquals(1, $response['data'][0]['id']);
    $this->assertEquals(3, $response['data'][1]['id']);

  }

  /** @test */
  public function a_piece_of_cloth_can_be_removed(){

    // display more accurate errors
    $this->withoutExceptionHandling();
    // create a Cloth
    $this->createCloth();

    // store response
    // as a user
    $response = $this->actingAs($this->createUser())
    // delete Cloth
    ->delete('api/clothes/' . Cloth::first()->id);

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // there are no Clothes in the DB
    $this->assertCount(0, Cloth::all());
  }

  /** @test */
  public function cloth_remove_returns_all_of_users_clothes(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Clothes via 2 different users.
    $this->createCloth();
    $this->createCloth();

    // Get the first user.
    $firstUser = User::first();

    // Create 2 new clothes as the first user.
    // As the first user
    $this->actingAs($firstUser)
    // create a new cloth.
    ->post('api/clothes/', [
      // New cloth.
      'title' => 'Third shirt',
      'description' => "Third description.",
      'category' => 1,
      'buy_at' => "Third Store",
      'buy_date' => "2020-03-03",
      'status' => 2,
    ]);

    // As the first user
    $this->actingAs($firstUser)
    // create a new cloth.
    ->post('api/clothes/', [
      // New cloth.
      'title' => 'Fourth shirt',
      'description' => "Fourth description.",
      'category' => 1,
      'buy_at' => "Fourth Store",
      'buy_date' => "2020-04-04",
      'status' => 2,
    ]);

    // As the first user
    $response = $this->actingAs(User::first())
    // remove first cloth.
    ->delete('api/clothes/' . Cloth::first()->id);

    // Response returned 2 clothes items.
    $this->assertCount(2, $response['data']);
    // 2 clothes both belong to the first user.
    $this->assertEquals(3, $response['data'][0]['id']);
    $this->assertEquals(4, $response['data'][1]['id']);

  }
}
