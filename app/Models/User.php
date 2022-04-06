<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

use App\Models\Cloth;
use App\Models\Day;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var string[]
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the clothes for the user.
   */
  public function clothes(){
    return $this->hasMany(Cloth::class);
  }

  /**
   * Get the days for the user.
   */
  public function days(){
    return $this->hasMany(Day::class);
  }

  /**
   * Generate verification token.
   */
  public function generateVerificationToken(){
    // Verification token.
    $token = null;

    // While token is null:
    while($token === null){
      // Set new token.
      $token = Str::random(10);

      // Number of Users with the given token.
      $numberOfUsersWithTheGivenToken = User::where(['token' => $token])->count();
      // There are Users with the given token.
      if($numberOfUsersWithTheGivenToken > 0){
        // Reset token.
        $token = null;
      }
    }
    
    // Set verification token.
    $this->email_verification_token = $token;
  }
}
