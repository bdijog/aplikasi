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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // Translatable fields — stored as JSON (spatie/laravel-translatable)
            $table->json('title');    // e.g. {"en": "Welcome", "id": "Selamat Datang"}
            $table->json('content');  // Rich text / full body, per locale
            $table->json('summary')->nullable(); // Short summary per locale

            // Meta / scheduling
            $table->string('slug')->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expired_at')->nullable();

            // Optional: featured image
            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
