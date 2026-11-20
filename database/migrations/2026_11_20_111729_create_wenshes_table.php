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
        Schema::create('wenshes', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone_code');
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_veryfaied')->default(false);
            $table->string('lisence_image')->nullable();
            $table->string('lisensce_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wenshes');
    }
};
