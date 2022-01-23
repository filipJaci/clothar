<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

use App\Models\Cloth;
use App\Models\User;

class ClothManagmentTest extends TestCase{

  // creates a User
  private function createUser(){
    return User::factory()->create();
  }

  // creates a Cloth
  private function createCloth(){
    // as a user
    return $this->actingAs($this->createUser())
    // create a Cloth
    ->post('/api/clothes',[
      'title' => 'Short Sleeves shirt',
      'description' => null,
      'category' => null,
      'buy_at' => null,
      'buy_date' => null,
      'status' => 1,
    ]);
  }

  // checks api repsonse format, wheter or not all keys are present
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
  public function a_piece_of_cloth_can_be_updated(){

    // display more accurate errors
    $this->withoutExceptionHandling();
    // create a Cloth
    $this->createCloth();

    // as a user
    $response = $this->actingAs($this->createUser())
    // update Cloth
    ->patch('api/clothes/' . Cloth::first()->id, [
      // new values
      'title' => 'Long Sleves shirt',
      'description' => "new description",
      'category' => 1,
      'buy_at' => "ACME Store",
      'buy_date' => "2020-10-20",
      'status' => 2,
    ]);

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // get updated Cloth from the DB
    $cloth = Cloth::first();

    // check if Cloth has been updated
    $this->assertEquals('Long Sleves shirt', $cloth->title);
    $this->assertEquals('new description', $cloth->description);
    $this->assertEquals(1, $cloth->category);
    $this->assertEquals('ACME Store', $cloth->buy_at);
    $this->assertEquals(Carbon::parse('2020-10-20'), $cloth->buy_date);
    $this->assertEquals(2, $cloth->status);

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
  public function a_piece_of_cloth_can_be_showed(){

    // display more accurate errors
    $this->withoutExceptionHandling();
    // create a Cloth
    $this->createCloth();

    // store response
    // as a user
    $response = $this->actingAs($this->createUser())
    // get Cloth from the DB
    ->get('api/clothes/' . Cloth::first()->id);

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // check if all Cloth keys are present
    $this->assertArrayHasKey('id', $response['data']);
    $this->assertArrayHasKey('created_at', $response['data']);
    $this->assertArrayHasKey('updated_at', $response['data']);
    $this->assertArrayHasKey('status', $response['data']);
    $this->assertArrayHasKey('title', $response['data']);
    $this->assertArrayHasKey('description', $response['data']);
    $this->assertArrayHasKey('category', $response['data']);
    $this->assertArrayHasKey('buy_at', $response['data']);
    $this->assertArrayHasKey('buy_date', $response['data']);
    $this->assertArrayHasKey('status', $response['data']);

  }

  /** @test */
  public function all_cloths_can_be_showed(){

    // display more accurate errors
    $this->withoutExceptionHandling();
    // create 2 Clothes
    $this->createCloth();
    $this->createCloth();

    // store response
    // as a user
    $response = $this->actingAs($this->createUser())
    // get all Clothes
    ->get('api/clothes/');

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // there are 2 Clothes fetched from the DB
    $this->assertCount(2, $response['data']);
  }
}
