<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('price_per_litre', 8, 2); // Price for 1 liter
            $table->decimal('price_per_20_litre', 8, 2); // Price for 20 liters (jumla)
            $table->decimal('price_of_mashudu', 8, 2); // Price for mashudu (per kg)
            $table->unsignedBigInteger('alizeti_id');
            $table->timestamps();

            // Define foreign key relation with alizeti table
            $table->foreign('alizeti_id','fk_prices_alizeti')->references('alizeti_id')->on('alizeti')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
