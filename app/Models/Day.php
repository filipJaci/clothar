<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cloth;
use Carbon\Carbon;

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
    * Formats date into the correct format whenever Day is being retrieved.
    */
    public function getDateAttribute($value){

        return Carbon::parse($value)->toDateString();

    }
}
