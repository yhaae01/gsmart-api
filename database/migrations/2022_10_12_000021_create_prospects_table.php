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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->year('year')->nullable();

            $table->unsignedBigInteger('transaction_type_id')->nullable();
            $table->index('transaction_type_id');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('prospect_type_id')->nullable();
            $table->index('prospect_type_id');
            $table->foreign('prospect_type_id')->references('id')->on('prospect_types')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('strategic_initiative_id')->nullable();
            $table->index('strategic_initiative_id');
            $table->foreign('strategic_initiative_id')->references('id')->on('strategic_initiatives')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('pm_id')->nullable();
            $table->index('pm_id');
            $table->foreign('pm_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('ams_customer_id')->nullable();
            $table->index('ams_customer_id');
            $table->foreign('ams_customer_id')->references('id')->on('ams_customers')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('prospects');
    }
};
