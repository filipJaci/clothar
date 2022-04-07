<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;

use App\Mail\EmailConfirmation;

class MailTest extends TestCase{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function an_email_can_be_sent(){

      // Prevent sending an actual email.
      Mail::fake();
      // Attempt to send an email.
      Mail::to('<email_address>')->send(new EmailConfirmation('1234abcd'));

      // An email can be sent.
      Mail::assertSent(EmailConfirmation::class);
    }
}
