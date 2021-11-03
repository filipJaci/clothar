<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'buyDate',
        'created_at',
        'updated_at'
    ];

    /**
     * Set the status attribute if it is missing from the request.
     *
     * @param  string  $value
     * @return number
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value === null ? 1 : $value;
    }

}
