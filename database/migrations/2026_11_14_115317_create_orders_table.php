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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_type_id')->constrained('car_types');
            $table->decimal('from_latitude', 10, 7);
            $table->decimal('from_longitude', 10, 7);
            $table->decimal('to_latitude', 10, 7);
            $table->decimal('to_longitude', 10, 7);
            $table->string('type')->default('normal');
            $table->string('status')->default('pending');
            $table->date('scheduled_day')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->json('payment_methods');
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('app_fee', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
