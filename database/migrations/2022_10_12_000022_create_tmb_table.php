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
        Schema::create('tmb', function (Blueprint $table) {
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

            $table->unsignedBigInteger('component_id')->nullable();
            $table->index('component_id');
            $table->foreign('component_id')->references('id')->on('component_id')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('engine_id')->nullable();
            $table->index('engine_id');
            $table->foreign('engine_id')->references('id')->on('engine_id')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('apu_id')->nullable();
            $table->index('apu_id');
            $table->foreign('apu_id')->references('id')->on('apu_id')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->index('maintenance_id');
            $table->foreign('maintenance_id')->references('id')->on('maintenances')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('igte_id')->nullable();
            $table->index('igte_id');
            $table->foreign('igte_id')->references('id')->on('igtes')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('learning_id')->nullable();
            $table->index('learning_id');
            $table->foreign('learning_id')->references('id')->on('learnings')->onDelete('cascade')->onUpdate('cascade');

            $table->decimal('market_share', 18,2)->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('tmb');
    }
};
