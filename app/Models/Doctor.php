<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Doctor extends Model
{
    use SoftDeletes;


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
    public function slots(): HasMany
    {
        return $this->hasMany(DoctorSlot::class);
    }

}
