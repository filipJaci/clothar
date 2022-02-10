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

    // Create a new Cloth and attach it to User.
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

  // Creates Day with Clothes worn on that day.
  private function createDayWithCloth($user, $date, $clothes){
    // As a User
    return $this->actingAs($user)
    // create Day with Clothes worn on that day.
    ->post('api/days', [
      'date' => $date,
      'clothes' => $clothes,
      // will be used in the future
      // 'ocassion' => 1
    ]);

  }

  // Updates Day with Clothes worn on that day.
  private function updateDayWithCloth($user, $dayId, $clothes){
    // As a User
    return $this->actingAs($user)
    // update Day with Clothes worn on that day.
    ->patch('api/days/' . $dayId, [
      'clothes' => $clothes,
      // will be used in the future
      // 'ocassion' => 1
    ]);

  }

  // Deletes Day with Clothes worn on that day.
  private function deleteDayWithCloth($user, $dayId){
    // As a User
    return $this->actingAs($user)
    // update Day with Clothes worn on that day.
    ->delete('api/days/' . $dayId);

  }

  // Checks api repsonse format, wheter or not all keys are present.
  private function checkResponseFormat($response){

    $this->assertArrayHasKey('title', $response);
    $this->assertArrayHasKey('message', $response);
    $this->assertArrayHasKey('write', $response);
    $this->assertArrayHasKey('data', $response);

    if(count($response['data']) > 0){
      $this->assertArrayHasKey('clothes', $response['data'][0]);
    }
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
    // As the User, on the given date, store Clothes.
    $response = $this->createDayWithCloth($user, '2021-12-12', [$clothId]);
    
    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);
    
    // There is 1 Day retrieved.
    $this->assertCount(1, $response['data']);
    // There are 1 Cloth stored on this Day.
    $this->assertCount(1, $response['data'][0]['clothes']);

  }

  /** @test */
  public function cloth_and_day_can_be_updated(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create 2 Clothes and attach it to the User.
    $clothId1 = $this->createClothAndGetId($user);
    $clothId2 = $this->createClothAndGetId($user);

    // Record the response.
    // As the User, on the given date, store Clothes.
    $response = $this->createDayWithCloth($user, '2021-12-12', [$clothId1]);
    // Store dayId of the created DayWithCloth.
    $dayId = $response['data'][0]['id'];
    
    // Record the response.
    // As the User, using a dayId, update Clothes.
    $response = $this->updateDayWithCloth($user, $dayId, [$clothId1, $clothId2]);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 Day retrieved.
    $this->assertCount(1, $response['data']);
    // There are 2 Clothes stored on this Day.
    $this->assertCount(2, $response['data'][0]['clothes']);
  }

  /** @test */
  public function cloth_and_day_can_be_deleted(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create a Cloth as the created User.
    $clothId = $this->createClothAndGetId($user);

    // Create multiple Days with Clothes
    // As the User, on the given date, store Clothes.
    $this->createDayWithCloth($user, '2021-12-12', [$clothId]);
    // Record the response.
    $response = $this->createDayWithCloth($user, '2021-12-13', [$clothId]);
    // Store dayId of the created DayWithCloth.
    $dayId = $response['data'][0]['id'];

    // Record the response.
    // As the User, using a dayId, delete Day.
    $response = $this->deleteDayWithCloth($user, $dayId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 Day retrieved.
    $this->assertCount(1, $response['data']);
  }

  /** @test */
  public function cloth_and_day_index_returns_all_clothes(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create a Cloth as the created User.
    $clothId = $this->createClothAndGetId($user);

    // Create multiple Days with Clothes
    // As the User, on the given date, store Clothes.
    $this->createDayWithCloth($user, '2021-12-12', [$clothId]);
    $this->createDayWithCloth($user, '2021-12-13', [$clothId]);
    
    // Record the response.
    // As a User
    $response = $this->actingAs($user)
    // update Day with Clothes worn on that day.
    ->get('api/days/');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There are 2 Days retrieved.
    $this->assertCount(2, $response['data']);
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

  /** @test */
  public function a_user_can_only_update_their_own_cloth_and_day(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create a Cloth and attach it to the user1.
    $clothId = $this->createClothAndGetId($user1);

    // As the user2, on the given date, store user1's Cltoh..
    $response = $this->createDayWithCloth($user2, '2021-12-12', [$clothId]);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  }
}
