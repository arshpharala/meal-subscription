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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('address_id')->nullable()->after('user_id');
            $table->text('notes')->nullable()->after('auto_charge');
            $table->date('start_date')->nullable()->after('next_charge_date');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('payment_method_id')->nullable()->after('stripe_price');
            $table->string('reference')->nullable()->after('stripe_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
};
