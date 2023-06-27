<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InatObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "observed_on",
        "inat_created_at",
        "inat_updated_at",
        "latitude",
        "longitude",
        "quality_grade",
        "license",
        "image_url",
        "num_identification_agreements",
        "num_identification_disagreements",
        "oauth_application_id",
        "nmw",
        "butterfly",
        "user_id",
        "taxa_id",
        "location_id",
    ];

    public function user()
    {
        return $this->belongsTo(InatUser::class);
    }

    public function taxa()
    {
        return $this->belongsTo(InatTaxa::class);
    }

    public function location()
    {
        return $this->belongsTo(InatLocation::class);
    }
}
