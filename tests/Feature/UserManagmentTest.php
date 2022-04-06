<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

use App\Mail\EmailConfirmation;

use App\Models\user;

class UserManagmentTest extends TestCase{

  // On setup
  protected function setUp() :void{
    parent::setUp();
    // disable middleware which limits number number of requests.
    $this->withoutMiddleware(
      ThrottleRequests::class
    );
  }

  use RefreshDatabase;

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
    return $this->post('/api/verify/', [
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

  // checks api repsonse format, wheter or not all keys are present
  private function checkResponseFormat($response){
    $this->assertArrayHasKey('title', $response);
    $this->assertArrayHasKey('message', $response);
    $this->assertArrayHasKey('write', $response);
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
    // New verification email was sent.
    Mail::assertSent(EmailConfirmation::class);
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
    $user = User::first();
    $user->email_verified = true;
    $user->save();

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

}
