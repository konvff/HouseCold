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
        Schema::table('payment_holds', function (Blueprint $table) {
            // Drop Stripe columns
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_payment_method_id',
                'stripe_customer_id'
            ]);

            // Add Cardknox columns
            $table->string('cardknox_transaction_id')->nullable()->after('status');
            $table->string('cardknox_auth_code')->nullable()->after('cardknox_transaction_id');
            $table->string('card_last_four', 4)->nullable()->after('cardknox_auth_code');
            $table->string('card_type')->nullable()->after('card_last_four');
            $table->timestamp('captured_at')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_holds', function (Blueprint $table) {
            // Drop Cardknox columns
            $table->dropColumn([
                'cardknox_transaction_id',
                'cardknox_auth_code',
                'card_last_four',
                'card_type',
                'captured_at'
            ]);

            // Restore Stripe columns
            $table->string('stripe_payment_intent_id')->nullable()->after('status');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_payment_method_id');
        });
    }
};
