<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InatLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "latitude",
        "longitude",
        "place_guess",
        "region",
        "state",
        "district"
    ];

    public function observations()
    {
        return $this->hasMany(InatObservation::class);
    }
}
