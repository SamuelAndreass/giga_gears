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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // JNE, J&T, SiCepat
            $table->string('service_type'); // REG, ECO, SDS, Cargo, Custom
            $table->string('custom_service')->nullable(); // opsional
            $table->decimal('base_rate', 10, 2);
            $table->decimal('per_kg', 10, 2)->default(0);
            $table->unsignedTinyInteger('min_delivery_days')->nullable();
            $table->unsignedTinyInteger('max_delivery_days')->nullable();
            $table->string('coverage')->default('Domestic (Indonesia)');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
