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
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->string('booking_code')->unique();
            $table->date('appointment_date');
            $table->time('estimated_time')->nullable();
            $table->string('visit_type')->default('new_visit');
            $table->text('chief_complaint')->nullable();
            $table->text('patient_notes')->nullable();
            $table->string('status')->default('pending');
            $table->string('source')->default('online');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('check_in_method')->nullable();
            $table->unsignedBigInteger('checked_in_by')->nullable()->comment('User ID (staf loket) yang memproses check-in');
            $table->unsignedBigInteger('created_by')->nullable()->comment('ID pembuat appointment (User / Doctor / null)');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['appointment_date', 'doctor_id', 'status']);
            $table->index(['patient_id', 'appointment_date']);
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
