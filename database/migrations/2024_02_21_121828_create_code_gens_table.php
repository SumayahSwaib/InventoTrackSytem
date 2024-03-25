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
        Schema::create('code_gens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('table_name')->nullable();
            $table->text('end_point')->nullable();
            $table->text('model_name')->nullable();
            $table->text('others')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_gens');
    }
};
