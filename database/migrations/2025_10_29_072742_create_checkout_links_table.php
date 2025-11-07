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
        Schema::create('checkout_links', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('meal_id');
            $table->foreignUuid('meal_package_id');
            $table->foreignUuid('meal_package_price_id');

            $table->date('start_date')->nullable();
            $table->boolean('is_recurring')->default(false);

            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_checkout_url')->nullable();
            $table->boolean('email_sent')->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_links');
    }
};
