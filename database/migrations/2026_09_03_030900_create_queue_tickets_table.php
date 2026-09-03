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
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('queue_date');
            $table->integer('queue_number');
            $table->string('prefix', 10)->default('A');
            $table->string('display_number', 20);
            $table->string('status')->default('waiting');
            $table->string('priority')->default('normal');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('call_count')->default(0);
            $table->string('counter')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['queue_date', 'doctor_id', 'status']);
            $table->unique(['queue_date', 'doctor_id', 'queue_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
