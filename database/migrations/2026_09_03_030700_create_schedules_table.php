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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->date('specific_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_patients')->default(20);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->string('type')->default('recurring');
            $table->timestamps();

            $table->index(['doctor_id', 'day_of_week', 'status']);
            $table->index(['doctor_id', 'specific_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
