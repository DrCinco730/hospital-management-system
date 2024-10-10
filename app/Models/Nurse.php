<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nurse extends Model
{
    use HasFactory;

    protected $table = 'nurses';
    protected $fillable = ['name', 'specialty', 'clinic_id', 'experience'];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
