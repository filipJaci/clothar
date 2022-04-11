<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Routing\Middleware\ThrottleRequests;

use App\Models\Cloth;
use App\Models\User;

class ClothManagmentTest extends TestCase{

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

  // Creates a Cloth as the User.
  private function createCloth($user){
    // As a user
    return $this->actingAs($user)
    // create a Cloth.
    ->post('/api/clothes',[
      'title' => $this->faker->word(),
      'description' => null,
      'category' => null,
      'buy_at' => null,
      'buy_date' => null,
      'status' => 1,
    ]);
  }
  
  // Updates a Cloth as the User.
  private function updateCloth($user, $clothId){
    // As a User
    return $this->actingAs($user)
    // update first Cloth.
    ->patch('api/clothes', [
      // New values.
      'id' => $clothId,
      'title' => 'Long Sleeves shirt',
      'description' => "new description",
      'category' => 1,
      'buy_at' => "ACME Store",
      'buy_date' => "2020-10-20",
      'status' => 2,
    ]);
  }

  // Deletes a Cloth as the User.
  private function deleteCloth($user, $clothId){
    // As a User
    return $this->actingAs($user)
    // update first Cloth.
    ->delete('api/clothes/' . $clothId);
  }

  // Gets all Clothes as the User.
  private function indexClothes($user){
    // As a user
    return $this->actingAs($user)
    // get all Clothes.
    ->get('api/clothes/');
  }

  // Checks api repsonse format, wheter or not all keys are present.
  private function checkResponseFormat($response){
    $this->assertArrayHasKey('scenario', $response);
    $this->assertArrayHasKey('data', $response);
  }

  use RefreshDatabase;

  /** @test */
  public function cloth_can_be_created(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Record the response.
    // Create a Cloth as the created User.
    $response = $this->createCloth($user);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check response format.
    $this->checkResponseFormat($response);

    // There is 1 Cloth retrieved.
    $this->assertCount(1, $response['data']);
  }

  /** @test */
  public function cloth_can_be_updated(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Record the response.
    // Create a Cloth as the created User.
    $response = $this->createCloth($user);

    // Get the clothId.
    $clothId = $response['data'][0]['id'];

    // As the User update Cloth.
    $response = $this->updateCloth($user, $clothId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check response format.
    $this->checkResponseFormat($response);

    // Store response data as cloth.
    $cloth = $response['data'][0];

    // Check if Cloth has been updated.
    $this->assertEquals('Long Sleeves shirt', $cloth['title']);
    $this->assertEquals('new description', $cloth['description']);
    $this->assertEquals(1, $cloth['category']);
    $this->assertEquals('ACME Store', $cloth['buy_at']);
    $this->assertEquals(Carbon::parse('2020-10-20'), Carbon::parse($cloth['buy_date']));
    $this->assertEquals(2, $cloth['status']);

  }

  /** @test */
  public function cloth_can_be_deleted(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create 2 Clothes.
    $this->createCloth($user);
    // Store the response on the second creation.
    $response = $this->createCloth($user);

    // Get the clothId.
    $clothId = $response['data'][0]['id'];

    // As the User delete Cloth.
    $response = $this->deleteCloth($user, $clothId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check response format.
    $this->checkResponseFormat($response);

    // There is 1 Cloth retrieved.
    $this->assertCount(1, $response['data']);
  }

  /** @test */
  public function cloth_index_returns_all_clothes(){
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create a User.
    $user = $this->createUser();

    // Create 2 Clothes.
    $this->createCloth($user);
    $this->createCloth($user);

    // Get all Clothes that belong to the user.
    $response = $this->indexClothes($user);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check response format.
    $this->checkResponseFormat($response);

    // There is 2 Clothes retrieved.
    $this->assertCount(2, $response['data']);
  }

  /** @test */
  public function cloth_create_returns_only_users_own_clothes(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create 2 Clothes for 2 Users.
    $this->createCloth($user1);
    $response = $this->createCloth($user2);

    // Store the response.
    // As a first user
    $response = $this->actingAs($user1)
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
  public function cloth_update_returns_only_users_own_clothes(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();

    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create 2 Clothes for 2 Users.
    $this->createCloth($user1);
    // Store the response.
    $response = $this->createCloth($user2);

    // Get clothId from response.
    $clothId = $response['data'][0]['id'];

    // Store the response.
    // Update Cloth as user2.
    $response = $this->updateCloth($user2, $clothId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 Cloth fetched from the DB.
    $this->assertCount(1, $response['data']);

  }

  /** @test */
  public function cloth_delete_returns_only_users_own_clothes(){
    
    // Display more accurate errors.
    $this->withoutExceptionHandling();

    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create 1 Cloth for user1 and 2 Clothes for user2.
    $this->createCloth($user1);
    $this->createCloth($user2);
    // Store the response.
    $response = $this->createCloth($user2);

    // Get clothId from response.
    $clothId = $response['data'][0]['id'];

    // Store the response.
    // Delete 1 Cloth as user2.
    $response = $this->deleteCloth($user2, $clothId);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 Cloth fetched from the DB.
    $this->assertCount(1, $response['data']);

  }

  /** @test */
  public function cloth_index_returns_only_users_own_clothes(){
    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();


    // Create 2 Clothes for user1 and 1 Cloth for user2.
    $this->createCloth($user1);
    $this->createCloth($user1);
    $this->createCloth($user2);

    // Get all Clothes that belong to the user1.
    $response = $this->indexClothes($user1);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check response format.
    $this->checkResponseFormat($response);

    // There is 2 Clothes retrieved.
    $this->assertCount(2, $response['data']);
  }

  /** @test */
  public function a_user_can_only_update_their_own_cloth(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create 2 Clothes for 2 Users.
    $this->createCloth($user1);
    // Store the response.
    $response = $this->createCloth($user2);

    // Get clothId from response.
    $clothId = $response['data'][0]['id'];

    // Attempt to update user2's Cloth as user1.
    $response = $this->updateCloth($user1, $clothId);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function a_user_can_only_delete_their_own_cloth(){

    // Display more accurate errors.
    $this->withoutExceptionHandling();
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();

    // Create 2 Clothes for 2 Users.
    $this->createCloth($user1);
    // Store the response.
    $response = $this->createCloth($user2);

    // Get clothId from response.
    $clothId = $response['data'][0]['id'];

    // Attempt to delete user2's Cloth as user1.
    $response = $this->deleteCloth($user1, $clothId);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  }
}
