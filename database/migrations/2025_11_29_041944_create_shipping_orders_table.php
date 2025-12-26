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
        Schema::create('shipping_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipping_id')->constrained('shippings')->onDelete('cascade');
            $table->string('tracking_number')->unique()->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('estimated_arrival_date')->nullable();
            $table->datetime('actual_arrival')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_orders');
    }
};
