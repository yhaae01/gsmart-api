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
        Schema::create('customer_swift', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string('code');
            $table->string('name');

            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->index('customer_group_id');
            $table->foreign('customer_group_id')->references('id')->on('customers')->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('customers_swift');
    }
};
