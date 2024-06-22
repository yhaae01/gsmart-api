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
        Schema::create('ams_targets', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('ams_id');
            $table->index('ams_id');
            $table->foreign('ams_id')->references('id')->on('ams');
            
            $table->year('year');
            $table->decimal('target');
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
        Schema::dropIfExists('a_m_s_targets');
    }
};
