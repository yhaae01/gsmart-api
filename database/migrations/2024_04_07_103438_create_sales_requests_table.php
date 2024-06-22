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
        Schema::create('sales_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
            $table->index('sales_id');

            $table->unsignedBigInteger('reviewer_id');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->index('reviewer_id');

            $table->integer('category');
            $table->integer('status');
            $table->string('reject_reason')->nullable();
            $table->boolean('commit');
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
        Schema::dropIfExists('sales_requests');
    }
};
