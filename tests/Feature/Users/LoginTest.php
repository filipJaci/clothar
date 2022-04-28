<?php

namespace Tests\Feature\Users;

use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;

use Tests\Feature\Helpers\UserTestHelper;

use App\Models\User;

class LoginTest extends TestCase{

    // On setup:
    protected function setUp() :void{
        parent::setUp();
        // disable middleware which limits number number of requests.
        $this->withoutMiddleware(
            ThrottleRequests::class
        );
    }

    /** @test */
    public function login_password_should_be_at_least_8_characters(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to login the User with a 7 characters password.
        $response = $userHelper->loginUser('user@mail.com', 'Pswd@12');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password must be at least 8 characters.', $response['data'][0]);
    }

    /** @test */
    public function login_password_should_be_at_most_40_characters(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to login the User with a 41 characters password.
        $response = $userHelper->loginUser('user@mail.com', Str::random(40).'!');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password must not be greater than 40 characters.', $response['data'][0]);
    }

    /** @test */
    public function login_password_should_contain_at_least_one_lowercase_letter(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to login the User without a lowercase character.
        $response = $userHelper->loginUser('user@mail.com', 'PSWD@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function login_password_should_contain_at_least_one_uppercase_letter(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Attempt to login the User without a lowercase character.
        $response = $userHelper->loginUser('user@mail.com', 'pswd@123');
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function unverified_user_can_not_log_in(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Login User.
        $response = $userHelper->loginUser('user@mail.com', 'Pswd@123');
        // Response HTTP status code is 403 - Forbidden.
        $response->assertStatus(403);
    }

    /** @test */
    public function login_email_validation_is_not_case_sensitive(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Login using the same email but in all capital letters.
        $response = $userHelper->loginUser('USER@mail.com', 'Pswd@123');
        // Response HTTP status code is ok.
        $response->assertOk();
    }

    /** @test */
    public function login_email_validation_removes_whitespaces(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Record the response.
        // Login using the same email but in all capital letters.
        $response = $userHelper->loginUser('user@mail.com        ', 'Pswd@123');
        // Response HTTP status code is ok.
        $response->assertOk();
    }

    /** @test */
    public function a_user_can_logout(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register and confirm email.
        $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Login User.
        $response = $userHelper->loginUser('user@mail.com', 'Pswd@123');
        // Get token.
        $token = 'Bearer ' . $response['data']['token'];
        // Record the response.
        // Logout User.
        $response = $this->withHeaders([
            'Authorization' => $token,
        ])->postJson('api/logout');
        // Response HTTP status code is ok.
        $response->assertOk();
    }

    
    // Should be moved to ClothIndexTest.
    // /** @test */
    // public function logged_in_user_can_only_access_their_own_data(){
    //     // Initiate UserTestHelper.
    //     $userHelper = new UserTestHelper();
    //     // Register and confirm email.
    //     $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
    //     // Create 2 Users.
    //     $user1 = $this->createUser();
    //     $user2 = $this->createUser();
    //     // Verify Users.
    //     $user1->email_verified = true;
    //     $user2->email_verified = true;
    //     // Save changes.
    //     $user1->save();
    //     $user2->save();
    //     // Create 2 Clothes for user1, 1 for user2 and store their ids.
    //     $clothId1 = $this->createClothAndGetId($user1);
    //     $clothId2 = $this->createClothAndGetId($user1);
    //     $clothId3 = $this->createClothAndGetId($user2);
    //     // Create a Days with Clothes for user1, 2 for user2.
    //     $dayId1 = $this->createClothWithDay($user1, '2022-04-11', [$clothId1, $clothId2]);
    //     $this->createClothWithDay($user2, '2022-04-11', [$clothId3]);
    //     $this->createClothWithDay($user2, '2022-04-12', [$clothId3]);
    //     // Get user1's email.
    //     $email = User::first()->email;
    //     // Record the response.
    //     // Login as user1.
    //     $response = $this->loginUser($email, 'User1234!');
    //     // There are 2 Clothes in the response, and 1 Day with Clothes.
    //     $this->assertCount(2, $response['data']['clothes']);
    //     $this->assertCount(1, $response['data']['days']);
    //     // Cloth ids match.
    //     $this->assertEquals($clothId1, $response['data']['clothes'][0]['id']);
    //     $this->assertEquals($clothId2, $response['data']['clothes'][1]['id']);
    //     // Days with Cloth ids match.
    //     $this->assertEquals($dayId1, $response['data']['days'][0]['id']);
    // }

}
