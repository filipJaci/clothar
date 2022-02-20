<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Routing\Middleware\ThrottleRequests;

use App\Models\Day;
use App\Models\Cloth;
use App\Models\User;

class ClothDayManagmentTest extends TestCase{

  // On setup
  protected function setUp() :void{
    parent::setUp();
    // disable middleware which limits number number of requests.
    $this->withoutMiddleware(
      ThrottleRequests::class
    );
  }

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

  // Creates Cloth with Day worn on that day.
  private function createClothWithDay($user, $date, $clothes){
    // As a User
    return $this->actingAs($user)
    // create Cloth with Day worn on that day.
    ->post('api/days', [
      'date' => $date,
      'clothes' => $clothes,
      // will be used in the future
      // 'ocassion' => 1
    ]);

  }

  // Updates Cloth with Day worn on that day.
  private function updateClothWithDay($user, $dayId, $clothes){
    // As a User
    return $this->actingAs($user)
    // update Cloth with Day worn on that day.
    ->patch('api/days/' . $dayId, [
      'clothes' => $clothes,
      // will be used in the future
      // 'ocassion' => 1
    ]);

  }

  // Deletes Cloth with Day worn on that day.
  private function deleteClothWithDay($user, $dayId){
    // As a User
    return $this->actingAs($user)
    // delete Cloth with Day worn on that day.
    ->delete('api/days/' . $dayId);

  }

  // Gets all of Days with Clothes that belong to the User.
  private function indexClothWithDay($user){
    // As a User
    return $this->actingAs($user)
    // get all of Days with Clothes that belong to that User.
    ->get('api/days/');
  }

  // Creates two Days with Clothes as the User.
  private function createMultipleClothWithDay($user){
    // Create Cloth and store clothId.
    $clothId = $this->createClothAndGetId($user);

    // Create two Days with Clothes as the User.
    $this->createClothWithDay($user, '2022-02-09', [$clothId]);
    return $this->createClothWithDay($user, '2022-02-10', [$clothId]);
  }

  // Creates a Days with Clothes with 2 Clothes as user1, and 1 Cloth as user2.
  private function createClothWithDayAsMultipleUsers($user1, $user2){
    // Create 3 Clothes, 2 for user1 and 1 for user2.
    $clothId1 = $this->createClothAndGetId($user1);
    $clothId2 = $this->createClothAndGetId($user1);
    $clothId3 = $this->createClothAndGetId($user2);

    // Store Clothes for each User.
    // As the User, on the given date, store clothes.
    $this->createClothWithDay($user1, '2021-12-12', [$clothId1]);
    $this->createClothWithDay($user2, '2021-12-12', [$clothId3]);
    // Return the response.
    return $this->createClothWithDay($user1, '2021-12-13', [$clothId2]);
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
  public function cloth_and_day_can_be_created(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Record the clothId.
    // Create a Cloth as the created User.
    $clothId = $this->createClothAndGetId($user);
    // There is 1 Cloth in the DB.
    $this->assertCount(1, Cloth::all());

    // Record the response.
    // As the User, on the given date, store Clothes.
    $response = $this->createClothWithDay($user, '2021-12-12', [$clothId]);
    
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
    $response = $this->createClothWithDay($user, '2021-12-12', [$clothId1]);
    // Store dayId of the created ClothWithDay.
    $dayId = $response['data'][0]['id'];
    
    // Record the response.
    // As the User, using a dayId, update Clothes.
    $response = $this->updateClothWithDay($user, $dayId, [$clothId1, $clothId2]);

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
    $this->createClothWithDay($user, '2021-12-12', [$clothId]);
    // Record the response.
    $response = $this->createClothWithDay($user, '2021-12-13', [$clothId]);
    // Store dayId of the created ClothWithDay.
    $dayId = $response['data'][0]['id'];

    // Record the response.
    // As the User, using a dayId, delete Day.
    $response = $this->deleteClothWithDay($user, $dayId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 Day retrieved.
    $this->assertCount(1, $response['data']);
  }

  /** @test */
  public function cloth_and_day_index_returns_all_days_with_clothes(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create two Days with Clothes as the User.
    $this->createMultipleClothWithDay($user);
    // Get all of Days with Clothes that belong to the User.
    $response = $this->indexClothWithDay($user);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There are 2 Days retrieved.
    $this->assertCount(2, $response['data']);
  }

  /** @test */
  public function cloth_and_day_create_returns_only_users_own_cloth_and_day(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Creates 2 Day with Cloth as user1, and 1 as user2.
    $response = $this->createClothWithDayAsMultipleUsers($user1, $user2);

    // There are 2 Days in the response.
    $this->assertCount(2, $response['data']);
  }

  /** @test */
  public function cloth_and_day_update_returns_only_users_own_cloth_and_day(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Creates two Days with Clothes as user1, and 1 as user2.
    $response = $this->createClothWithDayAsMultipleUsers($user1, $user2);
    // Store dayId from response.
    $dayId = $response['data'][0]['id'];
    // Create a new Cloth as the user1 and get id.
    $clothId = $this->createClothAndGetId($user1);

    // Update Day as the user1.
    $response = $this->updateClothWithDay($user1, $dayId, [$clothId]);

    // Cloth has been updated.
    $this->assertEquals($clothId, $response['data'][0]['clothes'][0]['id']);
  }

  /** @test */
  public function cloth_and_day_delete_returns_only_users_own_clothes_and_days(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Creates two Days with Clothes as user1, and 1 as user2.
    $response = $this->createClothWithDayAsMultipleUsers($user1, $user2);
    // Store dayId from response.
    $dayId = $response['data'][0]['id'];

    // Update Day as the user1.
    $response = $this->deleteClothWithDay($user1, $dayId);

    // There is 1 Day in the response.
    $this->assertCount(1, $response['data']);
  }

  /** @test */
  public function cloth_and_day_index_returns_only_users_own_cloth_and_day(){
  
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Creates two Days with Clothes as user1, and 1 as user2.
    $this->createClothWithDayAsMultipleUsers($user1, $user2);
    // Get all Cloths with Day for user1.
    $response = $this->indexClothWithDay($user1);

    // There are 2 Days in the response.
    $this->assertCount(2, $response['data']);
  }

  /** @test */
  public function removing_day_removes_cloth_and_day_pivot_data(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create a Cloth and attach it to the User.
    $clothId = $this->createClothAndGetId($user);

    // Record the response.
    // As the User, on the given date, store clothes.
    $response = $this->createClothWithDay($user, '2021-12-12', [$clothId]);
    
    // Get id of the inserted Day.
    $dayId = $response['data'][0]['id'];

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
  public function a_user_can_only_create_cloth_and_day_with_their_own_clothes(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create a Cloth and attach it to the user1.
    $clothId = $this->createClothAndGetId($user1);

    // As the user2, on the given date, store user1's Cloth.
    $response = $this->createClothWithDay($user2, '2021-12-12', [$clothId]);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function a_user_can_only_update_their_own_cloth_and_day(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Record the response.
    // Creates 2 Day with Cloth as user1, and 1 as user2.
    $response = $this->createClothWithDayAsMultipleUsers($user1, $user2);
    // Get dayId of the user1's first Day.
    $dayId = $response['data'][0]['id'];
    // Create a new Cloth as user2.
    $clothId = $this->createClothAndGetId($user2);

    // Update user1's day as user2.
    $response = $this->updateClothWithDay($user2, $dayId, [$clothId]);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function a_user_can_only_delete_their_own_cloth_and_day(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Record the response.
    // Creates 2 Day with Cloth as user1, and 1 as user2.
    $response = $this->createClothWithDayAsMultipleUsers($user1, $user2);
    // Get dayId of the user1's first Day.
    $dayId = $response['data'][0]['id'];

    // Delete user1's day as user2.
    $response = $this->deleteClothWithDay($user2, $dayId);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  }
}
