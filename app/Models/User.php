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

  // Used for case insensitive validation.
  public function setEmailAttribute($value)
  {
    $this->attributes['email'] = strtolower($value);
  }

  /**
   * Generate confirmation token.
   */
  public function generateConfirmationToken(){
    // Confirmation token.
    $token = null;

    // While token is null:
    while($token === null){
      // Set new token.
      $token = Str::random(10);

      // Number of Users with the given token.
      $numberOfUsersWithTheGivenToken = User::where(['email_confirmation_token' => $token])->count();
      // There are Users with the given token.
      if($numberOfUsersWithTheGivenToken > 0){
        // Reset token.
        $token = null;
      }
    }
    
    // Set confirmation token.
    $this->email_confirmation_token = $token;
  }
}
