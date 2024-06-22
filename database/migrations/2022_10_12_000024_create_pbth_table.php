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
        Schema::create('pbth', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->unsignedBigInteger('prospect_id')->nullable();
            $table->index('prospect_id');
            $table->foreign('prospect_id')->references('id')->on('prospects')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->index('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('ac_type_id')->nullable();
            $table->index('ac_type_id');
            $table->foreign('ac_type_id')->references('id')->on('ac_type_id')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('month')->nullable();
            $table->text('rate')->nullable();
            $table->text('flight_hour')->nullable();
            $table->decimal('market_share', 18, 2);
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
        Schema::dropIfExists('pbth');
    }
};
