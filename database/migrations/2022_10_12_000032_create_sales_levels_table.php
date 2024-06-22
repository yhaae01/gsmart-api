<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_levels', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_id');
            $table->index('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('level_id');
            $table->index('level_id');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade')->onUpdate('cascade');

            $table->tinyInteger('status');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_levels');
    }
};
