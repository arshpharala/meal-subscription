<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_renewal_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Linked Subscription
            $table->uuid('subscription_id')->index();

            // Reference to external transaction (Stripe, PayPal, Cash, etc.)
            $table->string('reference')->nullable()->index();

            // Payment gateway
            $table->unsignedBigInteger('gateway_id')->default(1)
                ->comment('References gateways table (Stripe, PayPal, Cash, etc.)');

            // Tax linkage (optional but powerful)
            $table->unsignedBigInteger('tax_id')->nullable()
                ->comment('References taxes table if applicable');

            // Currency reference
            $table->unsignedBigInteger('currency_id')
                ->comment('References currencies table');

            // Monetary values (in major units, not minor)
            $table->double('amount', 10, 2)->default(0)
                ->comment('Base amount before tax');
            $table->double('tax_amount', 10, 2)->default(0)
                ->comment('Tax amount applied');
            $table->double('total_amount', 10, 2)->default(0)
                ->comment('Total amount charged');

            // Result
            $table->enum('status', ['success', 'failed', 'pending'])
                ->default('pending')->index();
            $table->string('message')->nullable();
            $table->timestamp('charged_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_renewal_logs');
    }
};
