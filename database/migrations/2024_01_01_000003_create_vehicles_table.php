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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->constrained()->onDelete('cascade');
            $table->string('make'); // Toyota, Honda, etc.
            $table->string('model'); // Camry, Civic, etc.
            $table->integer('year');
            $table->string('license_plate')->unique();
            $table->string('color');
            $table->integer('seats');
            $table->text('features')->nullable(); // JSON or text
            $table->decimal('price_per_day', 10, 2);
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'booked', 'maintenance'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};