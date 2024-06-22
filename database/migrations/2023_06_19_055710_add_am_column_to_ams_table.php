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
        Schema::table('ams', function (Blueprint $table) {
            $table->unsignedBigInteger('am_id')->nullable()->after('user_id');
            $table->foreign('am_id')->references('id')->on('am')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ams', function (Blueprint $table) {
            //
        });
    }
};
