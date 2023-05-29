<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InatTaxa extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "scientific_name",
        "common_name",
        "order",
        "superfamily",
        "family",
        "subfamily",
        "tribe",
        "subtribe",
        "genus",
        "species",
        "subspecies",
        "variety",
        "form",
        "rank",
        "ancestry"
    ];

    public function observations()
    {
        return $this->hasMany(InatObservation::class);
    }
}
