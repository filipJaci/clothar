<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\Cloth;
use App\Models\User;

class ClothDayManagmentTest extends TestCase
{
  // creates a User
  private function createUser(){
    return User::factory()->create();
  }
  // creates cloth and returns id
  private function createClothAndGetId(){
    return Cloth::factory()->create()->id;
  }

  // creates day with cloth worn on that day
  private function createDayWithCloth(){
    // as a user
    return $this->actingAs($this->createUser())
    // creates day with cloth worn on that day
    ->post('api/days', [
      'date' => '2021-12-12',
      'clothes' => [$this->createClothAndGetId()],
      // will be used in the future
      // 'ocassion' => 1
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
  public function cloth_and_day_can_be_added(){
  
    // display more accurate errors
    $this->withoutExceptionHandling();
    // record response
    // create Day with Cloth
    $response = $this->createDayWithCloth();
    
    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);
    
    // there is 1 Day in the DB
    $this->assertCount(1, Day::all());
    // there is 1 Cloth in the DB
    $this->assertCount(1, Cloth::all());
    // Days can be accessed through Cloth
    $this->assertEquals(1, Cloth::first()->days()->count());

  }

  /** @test */
  public function cloth_and_day_update_syncs_database(){

    // display more accurate errors
    $this->withoutExceptionHandling();

    // create Day with Cloth
    $this->createDayWithCloth();
    // get first Day
    $day = Day::first();
    // create a new Cloth
    $newClothId = $this->createClothAndGetId();

    // record response
    // as a User
    $response = $this->actingAs($this->createUser())->
    // re-insert day with the new Cloth item
    post('api/days', [
      'date' => $day->date,
      'clothes' => [$newClothId],
      'ocassion' => 1
    ]);

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // there is 1 Cloth associated with edited Day
    $this->assertEquals(1, $day->clothes()->count());
    // id of the Cloth matches id of new Cloth
    $this->assertEquals($newClothId, $day->clothes()->first()->id);
  }

  /** @test */
  public function cloth_and_day_index_retrives_days_with_clothes(){

    // display more accurate errors
    $this->withoutExceptionHandling();

    // create 2 entries of Days with Clothes
    $this->createDayWithCloth();
    $this->createDayWithCloth();

    // record response
    // as a User
    $response = $this->actingAs($this->createUser())->
    // get all Days from the DB
    get('api/days/');

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // there are 2 entries of Days
    $this->assertCount(2, $response['data']);
    // Clothes have been retrieved with Days
    $this->assertArrayHasKey('clothes', $response['data'][0]);

  }

  /** @test */
  public function a_day_without_clothes_is_removed_from_the_database(){

    // display more accurate errors
    $this->withoutExceptionHandling();

    // create Day with Cloth
    $this->createDayWithCloth();
    // a Day can be retrieved through Cloth
    $this->assertEquals(1, Cloth::with('days')->first()->days->count());

    // get Day id
    $dayId = Day::first()->id;

    // record a response
    // as a User
    $response = $this->actingAs($this->createUser())->
    // delete Day entry
    delete('api/days/' . $dayId);

    // response HTTP status code is ok
    $response->assertOk();
    // check response format
    $this->checkResponseFormat($response);

    // deleting day also deleted related data from the cloth_day pivot table
    $this->assertDatabaseMissing('cloth_day', [
      'day_id' => $dayId
    ]);

  }
}
