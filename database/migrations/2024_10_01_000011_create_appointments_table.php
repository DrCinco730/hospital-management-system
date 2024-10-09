<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('patient_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('city_id')
                ->constrained('cities')
                ->onDelete('cascade');

            $table->foreignId('district_id')
                ->constrained('districts')
                ->onDelete('cascade');

            $table->foreignId('time_id')
                ->constrained('time_slots')
                ->onDelete('cascade');

            // Appointment Details
            $table->date('appointment_date');
            // If you need to store time as well, consider using dateTime
            // $table->dateTime('appointment_datetime');

            $table->enum('status', ['Pending', 'Done', 'Cancelled'])->default('Pending');

            $table->text('notes')->nullable();

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes for frequently queried columns
            $table->index('appointment_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
