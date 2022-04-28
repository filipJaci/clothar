<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;

use Tests\Feature\Helpers\UserTestHelper;

use App\Mail\EmailConfirmation;

use App\Models\User;

class EmailConfirmationTest extends TestCase{

    // On setup:
    protected function setUp() :void{
        parent::setUp();
        // disable middleware which limits number number of requests.
        $this->withoutMiddleware(
            ThrottleRequests::class
        );
    }

    /** @test */
    public function registration_sends_confirmation_email(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register User.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Confirmation email was sent.
        Mail::assertSent(EmailConfirmation::class);
    }

    /** @test */
    public function email_can_not_be_confirmed_8_hours_after_being_sent(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Register User.
        $userHelper->registerUser('user1234', 'user@mail.com', 'Pswd@123');
        // Get User data.
        $user = User::first();
        // Subtract updated_at by 8 hours.
        $user->updated_at = $user->updated_at->subHours(8);
        // Save changes.
        $user->save();
        // Record the response.
        // Verify email.
        $response = $userHelper->confirmEmail('user@mail.com');
        // Response HTTP status code is 410 - Gone (expired).
        $response->assertStatus(410);
        // New verification email was sent twice.
        Mail::assertSent(EmailConfirmation::class, 2);
    }

    /** @test */
    public function user_can_confirm_their_email(){
        // Initiate UserTestHelper.
        $userHelper = new UserTestHelper();
        // Record the response.
        // Register and confirm email.
        $response = $userHelper->registerAndConfirmEmail('user1234', 'user@mail.com', 'Pswd@123');
        // Response HTTP status code is ok.
        $response->assertOk();
        // Confirm that User is truly confirmed.
        $this->assertEquals(User::first()->value('email_confirmed'), 1);
    }



}
