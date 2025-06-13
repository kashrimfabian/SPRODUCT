<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricesIdAndTotalPriceToMauzoTable extends Migration
{
    
    public function up()
    {
        Schema::table('mauzo', function (Blueprint $table) {
             
            $table->decimal('total_price', 10, 2)->after('price'); 

            $table->unsignedBigInteger('prices_id')->after('mauzo_id');

            $table->foreign('prices_id','fk_mauzo_prices')->references('prices_id')->on('prices')->onDelete('cascade'); 
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('mauzo');
    }
}