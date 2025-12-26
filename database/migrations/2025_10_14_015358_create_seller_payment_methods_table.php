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
        Schema::create('seller_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('seller_store_id')->constrained('seller_stores')->onDelete('cascade');
            $table->string('payment_name');
            $table->string('provider');
            $table->string('account_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_payment_method');
    }
};
