<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->integer('rating'); 
            $table->text('comment');
            $table->boolean('is_verified')->default(false);
            $table->string('response_message')->nullable();
            $table->foreignId('responder_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('product_reviews'); }
};