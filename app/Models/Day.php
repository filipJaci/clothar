<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cloth;

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
     * Get the cloth that was worn on the date.
     */
    public function cloth()
    {
        return $this->belongsTo(Cloth::class);
    }
}
