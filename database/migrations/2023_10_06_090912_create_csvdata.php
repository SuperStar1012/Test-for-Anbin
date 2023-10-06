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
        Schema::create('csvdata', function (Blueprint $table) {
            $table->id();
            $table->string('UNIQUE_KEY')->nullable();
            $table->string('PRODUCT_TITLE')->nullable();
            $table->string('PRODUCT_DESCRIPTION')->nullable();
            $table->string('STYLE#')->nullable();
            $table->string('SANMAR_MAINFRAME_COLOR')->nullable();
            $table->string('SIZE')->nullable();
            $table->string('COLOR_NAME')->nullable();
            $table->string('PIECE_PRICE')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csvdata');
    }
};
