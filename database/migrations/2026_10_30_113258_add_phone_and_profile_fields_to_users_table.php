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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('phone')->nullable()->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->timestamp('profile_completed_at')->nullable();
            $table->timestamp('profile_skipped_at')->nullable();
            $table->boolean('requests_notifications_enabled')->default(true);
            $table->boolean('offers_notifications_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'emergency_contact_phone',
                'profile_photo_path',
                'profile_completed_at',
                'profile_skipped_at',
                'requests_notifications_enabled',
                'offers_notifications_enabled',
            ]);
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
