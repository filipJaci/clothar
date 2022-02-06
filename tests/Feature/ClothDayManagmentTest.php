<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\Cloth;
use App\Models\User;

class ClothDayManagmentTest extends TestCase
{
  // Laravel faker.
  use WithFaker;

  // Creates a User.
  private function createUser(){
    return User::factory()->create();
  }

  // Creates a Cloth and returns its id.
  private function createClothAndGetId($user){

    // Create a new cloth and attach it to user.
    $cloth = $user->clothes()->create([
      'title' => $this->faker->word(),
      'description' => null,
      'category' => null,
      'buy_at' => null,
      'buy_date' => null,
      'status' => 1,
    ])->save();

    // Get all Clothes.
    $allClothes = Cloth::all();

    // Return id of the last item in the allClothes array.
    return $allClothes[$allClothes->count() - 1]->id;
  }

  // Creates Day with Cloth worn on that day.
  private function createDayWithCloth($user, $date, $clothes){
    // As a User
    return $this->actingAs($user)
    // creates Day with Cloth worn on that day.
    ->post('api/days', [
      'date' => $date,
      'clothes' => $clothes,
      // will be used in the future
      // 'ocassion' => 1
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
  public function cloth_and_day_can_be_added(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create a Cloth as the created User.
    $clothId = $this->createClothAndGetId($user);
    // There is 1 Cloth in the DB.
    $this->assertCount(1, Cloth::all());

    // Record the response.
    // As the User, on the given date, store clothes.
    $response = $this->createDayWithCloth($user, '2021-12-12', [$clothId]);
    
    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);
    
    // There is 1 Day in the DB.
    $this->assertCount(1, Day::all());
    // Day can be accessed through Cloth.
    $this->assertEquals(1, Cloth::first()->days()->count());

  }

  /** @test */
  public function cloth_and_day_update_syncs_database(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create 2 Clothes and attach it to the User.
    $clothId1 = $this->createClothAndGetId($user);
    $clothId2 = $this->createClothAndGetId($user);

    // Store 2 Clothes on the same date, one at a time.
    // As the User, on the given date, store clothes.
    $this->createDayWithCloth($user, '2021-12-12', [$clothId1]);
    // Record the response.
    $response = $this->createDayWithCloth($user, '2021-12-12', [$clothId2]);
    
    // There is 1 Day entry.
    $this->assertCount(1, $response['data']);
    // There is 1 Cloth entry.
    $this->assertCount(1, $response['data'][0]['clothes']);
    // Cloth id matches the latest inserted Cloth.
    $this->assertEquals($clothId2, $response['data'][0]['clothes'][0]['id']);
  }

  /** @test */
  public function cloth_and_day_adding_returns_only_users_own_data(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create 3 Clothes, 2 for user1 and 1 for user2.
    $clothId1 = $this->createClothAndGetId($user1);
    $clothId2 = $this->createClothAndGetId($user2);
    $clothId3 = $this->createClothAndGetId($user1);

    // Store Clothes for each User.
    // As the User, on the given date, store clothes.
    $response1 = $this->createDayWithCloth($user1, '2021-12-12', [$clothId1, $clothId3]);
    $response2 = $this->createDayWithCloth($user2, '2021-12-12', [$clothId2]);

    // There are 2 Clothes retrieved for the user1. 
    $this->assertCount(2, $response1['data'][0]['clothes']);

    // Retrieved Cloth ids matches with users1's Clothes.
    $this->assertEquals($clothId1, $response1['data'][0]['clothes'][0]['id']);
    $this->assertEquals($clothId3, $response1['data'][0]['clothes'][1]['id']);

    // There is Cloth retrieved for the user2. 
    $this->assertCount(1, $response2['data'][0]['clothes']);
    // Retrieved Cloth id matches with users2's Cloth.
    $this->assertEquals($clothId2, $response2['data'][0]['clothes'][0]['id']);

  }

  /** @test */
  public function cloth_and_day_index_retrives_days_with_clothes(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();

    // Create a User.
    $user = $this->createUser();

    // Create a Cloth for the User.
    $clothId = $this->createClothAndGetId($user);

    // Store Cloth on different dates.
    // As the User, on the given date, store clothes.
    $this->createDayWithCloth($user, '2021-12-12', [$clothId]);
    $this->createDayWithCloth($user, '2021-12-13', [$clothId]);
    $this->createDayWithCloth($user, '2021-12-14', [$clothId]);

    // record response
    // As a User
    $response = $this->actingAs($user)->
    // get all Days from the DB.
    get('api/days/');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There are 3 entries of Days
    $this->assertCount(3, $response['data']);
    // Clothes have been retrieved with Days.
    $this->assertArrayHasKey('clothes', $response['data'][0]);

  }

  /** @test */
  public function a_day_without_clothes_is_removed_from_the_database(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create a Cloth and attach it to the User.
    $clothId = $this->createClothAndGetId($user);

    // Store Cloth on a date.
    // As the User, on the given date, store clothes.
    $this->createDayWithCloth($user, '2021-12-12', [$clothId]);
    
    // Get id of the inserted Day.
    $dayId = Day::first()->id;

    // Record the response.
    // As a User
    $response = $this->actingAs($user)->
    // delete Day entry.
    delete('api/days/' . $dayId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // Deleting Day also deleted related data from the cloth_day pivot table.
    $this->assertDatabaseMissing('cloth_day', [
      'day_id' => $dayId
    ]);

  }
}
