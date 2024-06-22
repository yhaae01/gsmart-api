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
        Schema::create('sales_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('sales');
            $table->foreignId('maintenance_id')->constrained('maintenances');
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
        Schema::dropIfExists('sales_maintenances');
    }
};
