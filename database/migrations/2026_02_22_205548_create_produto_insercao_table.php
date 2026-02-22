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
        Schema::create('produto_insercao', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category');
            $table->string('subcategory');
            $table->text('description');
            $table->string('manufacturer');
            $table->string('model');
            $table->string('color');
            $table->double('weight_g');
            $table->double('width_cm');
            $table->double('height_cm');
            $table->double('depth_cm');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_insercao');
    }
};
