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
        Schema::create('preco_insercao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produto_insercao_id');
            $table->decimal('price', 10, 2);
            $table->string('currency');
            $table->double('discount_percentage')->default(0);
            $table->double('increase_percentage')->default(0);
            $table->decimal('promotional_price', 10, 2)->nullable();
            $table->date('promotion_start_date')->nullable();
            $table->date('promotion_end_date')->nullable();
            $table->string('origin');
            $table->string('client_type');
            $table->string('seller_name');
            $table->text('observation')->nullable();

            $table->timestamps();

            $table->foreign('produto_insercao_id')
                ->references('id')
                ->on('produto_insercao')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preco_insercao');
    }
};
