<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralStaff extends Model
{
    use HasFactory;

    protected $table = 'general_staff';

    protected $fillable = ['name', 'role', 'clinic_id', 'experience'];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
