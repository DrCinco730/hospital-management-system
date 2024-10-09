<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{

    protected $fillable = [
        'name',
        'city_id',
        'district_id',
        'address',
        'phone',
    ];

    // Relationship with Doctor
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    // Relationship with City
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // Relationship with District
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
