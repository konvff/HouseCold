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
        Schema::table('sms_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('technician_id');
            $table->string('phone_number')->nullable()->after('message');
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound')->after('phone_number');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'technician_id']);
            $table->index(['appointment_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'technician_id']);
            $table->dropIndex(['appointment_id', 'type']);
            $table->dropColumn(['user_id', 'phone_number', 'direction']);
        });
    }
};
