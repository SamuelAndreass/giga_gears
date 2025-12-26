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
        Schema::create('seller_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('Cascade');
            $table->timestamps();
            $table->string('store_logo')->nullable();
            $table->string('store_banner')->nullable();
            $table->string('store_name');
            $table->string('store_phone')->nullable();
            $table->string('store_address')->nullable();
            $table->string('store_description')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('business_category')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->enum('status', ['active', 'inactive', 'banned', 'suspended', 'closed'])->default('active');
            $table->timestamp('verified_at')->nullable();$table->foreignId('verified_by')->nullable()->constrained('users', 'id')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
