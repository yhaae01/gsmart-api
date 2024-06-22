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
        Schema::create('sales_rejects', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_id');
            $table->index('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');

            $table->string('category');
            $table->text('reason');

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
        Schema::dropIfExists('sales_rejects');
    }
};
