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
        Schema::create('sales_reschedules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_id');
            $table->index('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('hangar_id')->nullable();
            $table->index('hangar_id');
            $table->foreign('hangar_id')->references('id')->on('hangars')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('line_id')->nullable();
            $table->index('line_id');
            $table->foreign('line_id')->references('id')->on('lines')->onDelete('cascade')->onUpdate('cascade');

            $table->date('start_date');
            $table->date('end_date');
            $table->integer('tat');
            $table->date('current_date');
            
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
        Schema::dropIfExists('sales_reschedules');
    }
};
