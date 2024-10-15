<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Doctor extends Authenticatable
{
    use SoftDeletes,HasFactory;


    protected $fillable = [
        'name',
        'specialty',
        'clinic_id',
        'experience_years',
        'email',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public $timestamps = false;


    // Relationship with Clinic
    public function clinic(): BelongsTo

    {
        return $this->belongsTo(Clinic::class);
    }
    public function slots(): HasMany
    {
        return $this->hasMany(DoctorSlot::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

}
