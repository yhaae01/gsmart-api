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
        Schema::create('sales_requirements', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('sales_id');
            $table->index('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('requirement_id');
            $table->index('requirement_id');
            $table->foreign('requirement_id')->references('id')->on('requirements')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('value');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('sales_requirements');
    }
};
