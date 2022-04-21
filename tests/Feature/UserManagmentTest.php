<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Mail\EmailConfirmation;
use App\Mail\ForgottenPasswordEmail;

use App\Models\User;
use App\Models\Cloth;
use App\Models\Day;

use function PHPUnit\Framework\assertEquals;

class UserManagmentTest extends TestCase{

  // Laravel faker.
  use WithFaker;

  use RefreshDatabase;

  // On setup
  protected function setUp() :void{
    parent::setUp();
    // disable middleware which limits number number of requests.
    $this->withoutMiddleware(
      ThrottleRequests::class
    );
  }


  // Registers a User.
  private function registerUser($name, $email, $password){
    // Prevent sending an actual email.
    Mail::fake();
    // Register User.
    return $this->post('api/register', [
      'name' => $name,
      'email' => $email,
      'password' => $password
    ]);
  }

  // Verifies a User.
  private function verifyEmail($email){
    // Get User verification token.
    $token = User::where('email', $email)->value('email_verification_token');

    // Verify email.
    return $this->post('/api/verify', [
      'token' => $token
    ]);
  }

  // Logs in User.
  private function loginUser($email, $password){

    // Return the response.
    // Login User.
    return $this->post('api/login', [
      'email' => $email,
      'password' => $password,
    ]);
  }

  // Creates a User.
  private function createUser(){
    return User::factory()->create();
  }

  // Sends a forgotten password request.
  private function sendAForgottenPasswordRequest($name, $email, $password, $register, $verify){
    // User should be registered.
    if($register){
      // Register User.
      $this->registerUser('User1234', 'user@mail.com', 'Pswd@123');
    }
    // User should be verified.
    if($verify){
      // Verify email.
      $this->verifyEmail('user@mail.com');
    }
    // Send a forgotten password request.
    return $this->post('api/forgot-password', [
      'email' => $email
    ]);
  }

  // Verify forgotten password request.
  private function verifyForgottenPasswordRequest($token, $password, $password_confirmation){
    return $this->patch('api/forgot-password', [
      'token' => $token,
      'password' => $password,
      'password_confirmation' => $password_confirmation
    ]);
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
    // Create Day entry for the User.
    $day = Day::create([
      'date' => new Carbon($date),
      'user_id' => $user->id
    ]);
    // For each Cloth:
    foreach($clothes as $cloth){
      // Save Clothes on given Day.
      $day->clothes()->attach($cloth, ['ocassion' => 1]);
    }
    // Return Day id.
    return $day->id;
  }

  // Checks API repsonse format, wheter or not all keys are present
  private function checkResponseFormat($response){
    $this->assertArrayHasKey('scenario', $response);
    $this->assertArrayHasKey('data', $response);

  }
  
  /** @test */
  public function a_user_can_register(){
    // Record the response.
    // Register User.
    $response = $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 User in the DB.
    $this->assertCount(1, User::all());
  }

  /** @test */
  public function registration_sends_confirmation_email(){
    // Register User.
    $response = $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Confirmation email was sent.
    Mail::assertSent(EmailConfirmation::class);
  }

  /** @test */
  public function registration_email_should_be_a_valid_email(){
    // Record the response.
    // Attempt to register the User with an invalid email.
    $response = $this->registerUser('user1234', 'usermail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The email must be a valid email address.', $response['data'][0]);
  }

  /** @test */
  public function registration_requries_a_unique_email(){

    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    
    // Record the response.
    // Attempt to register a User using already existing email.
    $response = $this->registerUser('user12345', 'user@mail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The email has already been taken.', $response['data'][0]);
  }

  /** @test */
  public function registration_email_validation_is_not_case_sensitive(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    
    // Record the response.
    // Attempt to re-register using the same email but in all capital letters.
    $response = $this->registerUser('user12345', 'USER@mail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The email must be lowercase.', $response['data'][0]);
  }

  /** @test */
  public function registration_validation_removes_whitespaces_from_request(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    
    // Record the response.
    // Attempt to re-register using the same email but with a whitespace character.
    $response = $this->registerUser('user12345', 'user@mail.com ', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The email has already been taken.', $response['data'][0]);
  }


  /** @test */
  public function registration_requries_a_unique_name(){

    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    
    // Record the response.
    // Attempt to register a User using already existing username.
    $response = $this->registerUser('user1234', 'user2@mail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The name has already been taken.', $response['data'][0]);
  }

  /** @test */
  public function registration_name_requries_an_alpha_dash_string(){
    // Record the response.
    // Attempt to register the User with a non-alpha dash name.
    $response = $this->registerUser('user#1234', 'user@mail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The name must only contain letters, numbers, dashes and underscores.', $response['data'][0]);
  }

  /** @test */
  public function registration_name_should_be_at_least_8_characters(){
    // Record the response.
    // Attempt to register the User with a 7 characters name.
    $response = $this->registerUser('user123', 'user@mail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The name must be at least 8 characters.', $response['data'][0]);
  }

  /** @test */
  public function registration_name_should_be_at_most_40_characters(){
    // Record the response.
    // Attempt to register the User with a 41 characters name.
    $response = $this->registerUser(Str::random(41), 'user@mail.com', 'Pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);


    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The name must not be greater than 40 characters.', $response['data'][0]);
  }

  /** @test */
  public function registration_password_should_be_at_least_8_characters(){
    // Record the response.
    // Attempt to register the User with a 7 characters password.
    $response = $this->registerUser('user1234', 'user@mail.com', 'Pswd@12');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password must be at least 8 characters.', $response['data'][0]);
  }

  /** @test */
  public function registration_password_should_be_at_most_40_characters(){
    // Record the response.
    // Attempt to register the User with a 41 characters password.
    $response = $this->registerUser('user1234', 'user@mail.com', Str::random(40).'!');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password must not be greater than 40 characters.', $response['data'][0]);
  }

  /** @test */
  public function registration_password_should_contain_at_least_one_lowercase_letter(){
    // Record the response.
    // Attempt to register the User without a lowercase character.
    $response = $this->registerUser('user1234', 'user@mail.com', 'PSWD@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function registration_password_should_contain_at_least_one_uppercase_letter(){
    // Record the response.
    // Attempt to register the User without a lowercase character.
    $response = $this->registerUser('user1234', 'user@mail.com', 'pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function registration_password_should_contain_at_least_one_special_character(){
    // Record the response.
    // Attempt to register the User without a lowercase character.
    $response = $this->registerUser('user1234', 'user@mail.com', 'pswd1234');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function user_can_verify_their_email(){
    $this->withoutExceptionHandling();
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');

    // Record the response.
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // Confirm that User is truly verified.
    $this->assertEquals(User::first()->value('email_verified'), 1);
  }

  /** @test */
  public function email_can_not_be_verified_8_hours_after_being_sent(){
    $this->withoutExceptionHandling();
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Get User data.
    $user = User::first();
    // Subtract updated_at by 8 hours.
    $user->updated_at = $user->updated_at->subHours(8);
    // Save changes.
    $user->save();

    // Record the response.
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Response HTTP status code is 410 - Gone (expired).
    $response->assertStatus(410);
    // Check the response format.
    $this->checkResponseFormat($response);
    // New verification email was sent twice.
    Mail::assertSent(EmailConfirmation::class, 2);
  }

  /** @test */
  public function unverified_user_can_not_log_in(){
    $this->withoutExceptionHandling();
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');

    // Record the response.
    // Login User.
    $response = $this->loginUser('user@mail.com', 'Pswd@123');

    // Response HTTP status code is 403 - Forbidden.
    $response->assertStatus(403);
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function verified_user_can_log_in(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify User.
    $this->verifyEmail('user@mail.com');

    // Record the response.
    // Login User.
    $response = $this->loginUser('user@mail.com', 'Pswd@123');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // Check data response format.
    // Token is present.
    $this->assertArrayHasKey('token', $response['data']);
    // User data is present.
    $this->assertArrayHasKey('user', $response['data']);
    // User data has all of the valid keys.
    $this->assertArrayHasKey('id', $response['data']['user']);
    $this->assertArrayHasKey('name', $response['data']['user']);
    $this->assertArrayHasKey('clothes', $response['data']);
    $this->assertArrayHasKey('days', $response['data']);

  }

  /** @test */
  public function logged_in_user_can_only_access_their_own_data(){
    // Create 2 Users.
    $user1 = $this->createUser();
    $user2 = $this->createUser();
    // Verify Users.
    $user1->email_verified = true;
    $user2->email_verified = true;
    // Save changes.
    $user1->save();
    $user2->save();
    // Create 2 Clothes for user1, 1 for user2 and store their ids.
    $clothId1 = $this->createClothAndGetId($user1);
    $clothId2 = $this->createClothAndGetId($user1);
    $clothId3 = $this->createClothAndGetId($user2);
    // Create a Days with Clothes for user1, 2 for user2.
    $dayId1 = $this->createClothWithDay($user1, '2022-04-11', [$clothId1, $clothId2]);
    $this->createClothWithDay($user2, '2022-04-11', [$clothId3]);
    $this->createClothWithDay($user2, '2022-04-12', [$clothId3]);
    // Get user1's email.
    $email = User::first()->email;
    // Record the response.
    // Login as user1.
    $response = $this->loginUser($email, 'User1234!');
    // There are 2 Clothes in the response, and 1 Day with Clothes.
    $this->assertCount(2, $response['data']['clothes']);
    $this->assertCount(1, $response['data']['days']);
    // Cloth ids match.
    $this->assertEquals($clothId1, $response['data']['clothes'][0]['id']);
    $this->assertEquals($clothId2, $response['data']['clothes'][1]['id']);
    // Days with Cloth ids match.
    $this->assertEquals($dayId1, $response['data']['days'][0]['id']);
  }

  /** @test */
  public function login_password_should_be_at_least_8_characters(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Record the response.
    // Attempt to login the User with a 7 characters password.
    $response = $this->loginUser('user@mail.com', 'Pswd@12');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password must be at least 8 characters.', $response['data'][0]);
  }

  /** @test */
  public function login_password_should_be_at_most_40_characters(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Record the response.
    // Attempt to login the User with a 41 characters password.
    $response = $this->loginUser('user@mail.com', Str::random(40).'!');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password must not be greater than 40 characters.', $response['data'][0]);
  }

  /** @test */
  public function login_password_should_contain_at_least_one_lowercase_letter(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Record the response.
    // Attempt to login the User without a lowercase character.
    $response = $this->loginUser('user@mail.com', 'PSWD@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function login_password_should_contain_at_least_one_uppercase_letter(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Record the response.
    // Attempt to login the User without a lowercase character.
    $response = $this->loginUser('user@mail.com', 'pswd@123');

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);

    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function login_email_validation_is_not_case_sensitive(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify User.
    $this->verifyEmail('user@mail.com');
    
    // Record the response.
    // Login using the same email but in all capital letters.
    $response = $this->loginUser('USER@mail.com', 'Pswd@123');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function login_email_validation_removes_whitespaces(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify User.
    $this->verifyEmail('user@mail.com');
    
    // Record the response.
    // Login using the same email but in all capital letters.
    $response = $this->loginUser('user@mail.com ', 'Pswd@123');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function a_user_can_logout(){
    // Register User.
    $this->registerUser('user1234', 'user@mail.com', 'Pswd@123');
    // Verify email.
    $response = $this->verifyEmail('user@mail.com');

    // Login User.
    $response = $this->loginUser('user@mail.com', 'Pswd@123');

    // Get token.
    $token = 'Bearer ' . $response['data']['token'];

    // Record the response.
    // Logout User.
    $response = $this->withHeaders([
      'Authorization' => $token,
    ])->postJson('api/logout');

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);
    
  }

  /** @test */
  public function forgotten_password_fails_if_email_is_invalid_or_doesnt_exist(){
    // Send a forgotten password request.
    // User should not be registered or verified.
    $response = $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', false, false);
    // Response HTTP status code is 400 - Bad request.
    $response->assertStatus(400);
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten.failed.email');
    // Check the response format.
    $this->checkResponseFormat($response);
  }

  /** @test */
  public function an_unverified_user_cannot_send_a_forgotten_email_request(){
    // Record the response.
    // Send a forgotten password request.
    // User should be registered, but not verified.
    $response = $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, false);

    // Response HTTP status code is 409 - Conflict.
    $response->assertStatus(409);
    // Check the response format.
    $this->checkResponseFormat($response);
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten.failed.unverified');
  }

  /** @test */
  public function forgotten_password_link_expires_after_8_hours_after_its_sent(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Mock that more than 8 hours have passed since the entry was created in the DB.
    DB::table('password_resets')->where('email', 'user@mail.com')->update([
      'created_at' => Carbon::now()->subHours(8)
    ]);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->select('token')->first()->token;
    // Set a new password.
    $newPassword = 'NewPswd@123';

    // Record the response.
    // Attempt to change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);
    // Response HTTP status code is 410 - Gone (expired).
    $response->assertStatus(410);
    // Check the response format.
    $this->checkResponseFormat($response);
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten.failed.expired');
    // New verification email was sent twice.
    Mail::assertSent(ForgottenPasswordEmail::class, 2);
  }

  /** @test */
  public function forgotten_password_deletes_forgotten_password_entry_after_use(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->select('token')->first()->token;
    // Set a new password.
    $newPassword = 'NewPswd@123';

    // Change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);
    // Record the response.
    // Attempt to change the password again with the same credentials.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);

    // Response HTTP status code is 400 - Bad request.
    $response->assertStatus(400);
    // Check the response format.
    $this->checkResponseFormat($response);
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten-password.failed.token');
  }

  /** @test */
  public function a_user_can_change_their_password_through_forgotten_password(){
    // Record the response.
    // Send a forgotten password request.
    // User should be registered and verified.
    $response = $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);
    // Forgotten password email was sent.
    Mail::assertSent(ForgottenPasswordEmail::class);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->first()->token;
    // Set a new password.
    $newPassword = 'NewPswd@123';

    // Record the response.
    // Change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);
    // Response HTTP status code is ok.
    $response->assertOk();
    // Check the response format.
    $this->checkResponseFormat($response);

    // Record the response.
    // Attempt to login with a new password.
    $response = $this->loginUser('user@mail.com', 'NewPswd@123');
    // Response HTTP status code is ok.
    $response->assertOk();    
  }
  
  /** @test */
  public function forgotten_password_new_password_should_be_at_most_40_characters(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->first()->token;
    // Set a new 41 character password.
    $newPassword = Str::random(40).'!';

    // Record the response.
    // Attempt to change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password must not be greater than 40 characters.', $response['data'][0]);
  }

  /** @test */
  public function forgotten_password_new_password_should_contain_at_least_one_lowercase_letter(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->first()->token;
    // Set a new password without any lowercase letters.
    $newPassword = 'NEWPSWD@123';

    // Record the response.
    // Attempt to change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
    // dd($response);
  
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function forgotten_password_new_password_should_contain_at_least_one_uppercase_letter(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->first()->token;
    // Set a new password without any uppercase letters.
    $newPassword = 'newpswd@123';

    // Record the response.
    // Attempt to change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
    // dd($response);
  
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function forgotten_password_new_password_should_contain_at_least_one_special_character(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->first()->token;
    // Set a new password without any special character.
    $newPassword = 'NewPswd123';

    // Record the response.
    // Attempt to change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
    // dd($response);
  
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password format is invalid.', $response['data'][0]);
  }

  /** @test */
  public function forgotten_password_new_password_should_be_at_least_8_characters(){
    // Send a forgotten password request.
    // User should be registered and verified.
    $this->sendAForgottenPasswordRequest('User1234', 'user@mail.com', 'Pswd@123', true, true);

    // Get forgotten password token.
    $token = DB::table('password_resets')->where('email', 'user@mail.com')->first()->token;
    // Set a new 7 character password.
    $newPassword = 'NewPw@1';

    // Record the response.
    // Attempt to change password through forgotten password.
    $response = $this->verifyForgottenPasswordRequest($token, $newPassword, $newPassword);

    // Response HTTP status code is 422 - invalid data.
    $response->assertStatus(422);
    // Check the response format.
    $this->checkResponseFormat($response);
  
    // API returned the correct scenario.
    $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
    // There is 1 error.
    $this->assertCount(1, $response['data']);
    // The error is the correct one.
    $this->assertEquals('The password must be at least 8 characters.', $response['data'][0]);
  }
}
