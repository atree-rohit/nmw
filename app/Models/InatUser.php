<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InatUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "login",
        "name"
    ];

    public function observations()
    {
        return $this->hasMany(InatObservation::class);
    }
}
