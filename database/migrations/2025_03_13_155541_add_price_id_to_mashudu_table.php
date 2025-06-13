<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceIdToMashuduTable extends Migration
{
    
    public function up()
    {
        Schema::table('mashudu', function (Blueprint $table) {
            $table->unsignedBigInteger('price_id')->after('mashudu_id'); 
            $table->foreign('price_id','fk_price_mashudu')->references('prices_id')->on('prices')->onDelete('cascade'); 
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('mashudu'); 
    }
}