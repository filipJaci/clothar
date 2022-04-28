<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Tests\Feature\Helpers\UserTestHelper;

use App\Mail\ForgottenPasswordEmail;

use App\Models\User;

class ForgottenPasswordTest extends TestCase
{
    // On setup:
    protected function setUp() :void{
        parent::setUp();
        // disable middleware which limits number number of requests.
        $this->withoutMiddleware(
            ThrottleRequests::class
        );
    }

    /** @test */
    public function forgotten_password_fails_if_email_is_invalid_or_doesnt_exist(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $response = $userHelper->sendAForgottenPasswordRequest('user@mail.com');
        // Response HTTP status code is 400 - Bad request.
        $response->assertStatus(400);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten.failed.email');
    }

    /** @test */
    public function user_that_has_not_confirmed_their_email_cannot_send_a_forgotten_password_request(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $response = $userHelper->registerSendForgottenPasswordRequest('user1234', 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is 409 - Conflict.
        $response->assertStatus(409);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten.failed.not-confirmed');
    }

    /** @test */
    public function user_can_make_a_forgotten_password_request(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', 'user@mail.com', 'Pswd@123');
        // Forgotten password email was sent.
        Mail::assertSent(ForgottenPasswordEmail::class);
    }

    /** @test */
    public function forgotten_password_verification_fails_if_token_is_invalid(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a verify forgotten password request with an invalid token.
        $response = $userHelper->verifyForgottenPasswordToken(Str::random(10));
        // Response HTTP status code is 400 - Bad request.
        $response->assertStatus(400);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten.failed.token');
    }

    /** @test */
    public function forgotten_password_verification_passes_if_token_is_valid(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a verify forgotten password request with an invalid token.
        $response = $userHelper->getAndVerifyForgottenPasswordToken('user@mail.com');
        // Get forgotten password token.
        // Response HTTP status code is ok.
        $response->assertOk();
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten.success.token');
    }

    /** @test */
    public function forgotten_password_link_expires_after_8_hours_after_its_sent(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Mock that more than 8 hours have passed since the entry was created in the DB.
        DB::table('password_resets')->where('email', $email)->update([
            'created_at' => Carbon::now()->subHours(8)
        ]);
        // Set a new password.
        $newPassword = 'NewPswd@123';
        // Attempt to change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is 410 - Gone (expired).
        $response->assertStatus(410);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten.failed.expired');
        // New verification email was sent twice.
        Mail::assertSent(ForgottenPasswordEmail::class, 2);
    }

    /** @test */
    public function forgotten_password_new_password_should_be_at_least_8_characters(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new 7 character password.
        $newPassword = 'NewPw@1';
        // Attempt to change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password must be at least 8 characters.', $response['data'][0]);
    }

    /** @test */
    public function forgotten_password_new_password_should_be_at_most_40_characters(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new 41 character password.
        $newPassword = Str::random(40).'!';
        // Attempt to change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password must not be greater than 40 characters.', $response['data'][0]);
    }

    /** @test */
    public function forgotten_password_new_password_should_contain_at_least_one_lowercase_letter(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new password without any lowercase letters.
        $newPassword = 'NEWPSWD@123';
        // Attempt to change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function forgotten_password_new_password_should_contain_at_least_one_uppercase_letter(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new password without any uppercase letters.
        $newPassword = 'newpswd@123';
        // Attempt to change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function forgotten_password_new_password_should_contain_at_least_one_special_character(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new password without any special character.
        $newPassword = 'NewPswd123';
        // Attempt to change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is 422 - invalid data.
        $response->assertStatus(422);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten-password.failed.password');
        // There is 1 error.
        $this->assertCount(1, $response['data']);
        // The error is the correct one.
        $this->assertEquals('The password format is invalid.', $response['data'][0]);
    }

    /** @test */
    public function a_user_can_change_their_password_through_forgotten_password(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new password.
        $newPassword = 'NewPswd@123';
        // Change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Response HTTP status code is ok.
        $response->assertOk();
        // Attempt to login with a new password.
        $response = $userHelper->loginUser($email, $newPassword);
        // Response HTTP status code is ok.
        $response->assertOk();
    }

    /** @test */
    public function forgotten_password_deletes_forgotten_password_entry_after_use(){
        // Set email.
        $email = 'user@mail.com';
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Send a forgotten password request.
        $userHelper->registerConfirmSendAForgottenPasswordRequest('user1234', $email, 'Pswd@123');
        // Set a new password.
        $newPassword = 'NewPswd@123';
        // Change password through forgotten password.
        $response = $userHelper->changePasswordThroughForgottenPassword($email, $newPassword, $newPassword);
        // Attempt to change the password again with the same credentials.
        $response = $userHelper->changePasswordThroughForgottenPasswordInvalid($email, $newPassword, $newPassword);
        // Response HTTP status code is 400 - Bad request.
        $response->assertStatus(400);
        // API returned the correct scenario.
        $this->assertEquals($response['scenario'], 'forgotten-password.failed.token');
    }
}