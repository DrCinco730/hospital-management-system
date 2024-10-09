<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Doctor extends Model
{

    protected $fillable = [
        'name',
        'specialty',
        'clinic_id',
        'experience_years',
    ];

    // Relationship with Clinic
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
