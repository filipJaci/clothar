<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable{

  use Queueable, SerializesModels;

  // Verification token.
  public $token;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($token){
    // Set token.
    $this->token = $token;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build(){
    return $this->markdown('emails.EmailConfirmation');
  }
}
