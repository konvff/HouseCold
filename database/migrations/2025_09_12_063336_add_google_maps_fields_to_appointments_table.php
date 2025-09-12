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
            $table->decimal('address_lat', 10, 8)->nullable()->after('customer_address');
            $table->decimal('address_lng', 11, 8)->nullable()->after('address_lat');
            $table->json('address_components')->nullable()->after('address_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['address_lat', 'address_lng', 'address_components']);
        });
    }
};
