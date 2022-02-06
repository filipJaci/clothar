<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\Models\Cloth;
use App\Models\Day;

class Day extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $dates = [
    'date',
    'created_at',
    'updated_at'
  ];

  /**
  * The clothes were worn on a date.
  */
  public function clothes(){
    return $this->belongsToMany(Cloth::class);
  }

  /**
   * Get the user that owns the day.
   */
  public function user(){
    return $this->belongsTo(User::class);
  }
}
