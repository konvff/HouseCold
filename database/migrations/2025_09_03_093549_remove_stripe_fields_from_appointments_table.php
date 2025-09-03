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
        Schema::table('appointments', function (Blueprint $table) {
            // Drop Stripe columns
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_payment_method_id',
                'stripe_customer_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Restore Stripe columns
            $table->string('stripe_payment_intent_id')->nullable()->after('actual_cost');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_payment_method_id');
        });
    }
};
