<?php

namespace Tests\Feature\Users;

use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;

use Tests\Feature\Helpers\UserTestHelper;

use App\Models\User;

class RegistrationTest extends TestCase{

    // On setup:
    protected function setUp() :void{
        parent::setUp();
        // disable middleware which limits number number of requests.
        $this->withoutMiddleware(
            ThrottleRequests::class
        );
    }

    /** @test */
    public function registration_email_should_be_a_valid_email(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User with an invalid email.
        $response = $userHelper->registerUser('user1234', 'usermail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The email must be a valid email address.', $response['data'][0]);
    }

    /** @test */
    public function registration_requries_a_unique_email(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register the User.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to register a User using already existing email.
        $response = $userHelper->registerUser('user12345', 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The email has already been taken.', $response['data'][0]);
    }

    /** @test */
    public function registration_email_validation_is_not_case_sensitive(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register User.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to re-register using the same email but in all capital letters.
        $response = $userHelper->registerUser('user12345', 'USER@mail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The email must be lowercase.', $response['data'][0]);
    }

    /** @test */
    public function registration_validation_removes_whitespaces_from_request(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register User.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to re-register using the same email but with a whitespace character.
        $response = $userHelper->registerUser('user12345', 'user@mail.com ', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The email has already been taken.', $response['data'][0]);
    }


    /** @test */
    public function registration_requries_a_unique_name(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register User.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to register a User using already existing username.
        $response = $userHelper->registerUser('user1234', 'user2@mail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The name has already been taken.', $response['data'][0]);
    }

    /** @test */
    public function registration_name_requries_an_alpha_dash_string(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User with a non-alpha dash name.
        $response = $userHelper->registerUser('user#1234', 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The name must only contain letters, numbers, dashes and underscores.', $response['data'][0]);
    }

    /** @test */
    public function registration_name_should_be_at_least_8_characters(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User with a 7 characters name.
        $response = $userHelper->registerUser('user123', 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The name must be at least 8 characters.', $response['data'][0]);
    }

    /** @test */
    public function registration_name_should_be_at_most_40_characters(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User with a 41 characters name.
        $response = $userHelper->registerUser(Str::random(41), 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The name must not be greater than 40 characters.', $response['data'][0]);
    }

    /** @test */
    public function registration_password_should_be_at_least_8_characters(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User with a 7 characters password.
        $response = $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@12');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password must be at least 8 characters.', $response['data'][0]);
    }

    /** @test */
    public function registration_password_should_be_at_most_40_characters(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User with a 41 characters password.
        $response = $userHelper->registerUser('user1234', 'user@mail.com', Str::random(40).'!');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password must not be greater than 40 characters.', $response['data'][0]);
    }

    /** @test */
    public function registration_password_should_contain_at_least_one_lowercase_letter(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User without a lowercase character.
        $response = $userHelper->registerUser('user1234', 'user@mail.com', 'PSWD@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function registration_password_should_contain_at_least_one_uppercase_letter(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User without a lowercase character.
        $response = $userHelper->registerUser('user1234', 'user@mail.com', 'pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function registration_password_should_contain_at_least_one_special_character(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Attempt to register the User without a lowercase character.
        $response = $userHelper->registerUser('user1234', 'user@mail.com', 'pswd1234');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function a_user_can_register(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Register User.
        $response = $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is ok.
        $response->assertOk();
        // There is 1 User in the DB.
        $this->assertCount(1, User::all());
    }

}
