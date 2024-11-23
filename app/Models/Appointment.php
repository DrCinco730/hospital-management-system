<?php

namespace App\Models;

use App\Traits\DetectsRelationships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class Appointment extends Model
{
    use HasFactory, SoftDeletes,DetectsRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'time_id',
        'appointment_date',
        'status',
        'type',
        'notes',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
        'created_at'
    ];

    /**
     * Get the patient associated with the appointment.
     *
     * @return BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the time slot associated with the appointment.
     *
     * @return BelongsTo
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'time_id');
    }

    /**
     * Get the doctor associated with the appointment.
     *
     * @return BelongsTo
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Booted method to add global scopes and set model events.
     */
    protected static function booted()
    {
        // Global scope to ignore soft-deleted records
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });

        parent::boot();

        // Model event for saving logic
        static::saving(function ($model) {
            $today = Carbon::today()->startOfDay();
            $appointmentDate = Carbon::parse($model->appointment_date)->startOfDay();

            // Soft delete if appointment date is in the past
            if ($appointmentDate->lessThan($today)) {
                $model->deleted_at = $appointmentDate;
            }

            // Soft delete for specific statuses
            if (in_array($model->status, ['Done', 'Cancelled', 'Completed', 'ToPharmacy'])) {
                $model->deleted_at = $model->appointment_date;
            }

            // Check for conflicting appointments when status is "Pending"
            if ($model->status === 'Pending') {
                $exists = self::where('appointment_date', $model->appointment_date)
                    ->where('time_id', $model->time_id)
                    ->where('doctor_id', $model->doctor_id)
                    ->whereNull('deleted_at')
                    ->where('type', $model->type)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'error' => 'This appointment slot is already taken for the specified status.',
                    ]);
                }
            }
        });
    }
}
