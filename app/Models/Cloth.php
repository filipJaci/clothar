<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\User;

class Cloth extends Model
{
  use HasFactory;

  protected $guarded = [];

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'clothes';

  protected $dates = [
    'buy_date',
    'created_at',
    'updated_at'
  ];

  /**
   * Set the status attribute if it is missing from the request.
   *
   * @param  string  $value
   * @return number
   */
  public function setStatusAttribute($value){
    $this->attributes['status'] = $value === null ? 1 : $value;
  }

  /**
   * The days cloth was worn on.
   */
  public function days(){
    return $this->belongsToMany(Day::class);
  }

  /**
   * Get the user that owns the cloth.
   */
  public function user(){
    return $this->belongsTo(User::class);
  }

}
