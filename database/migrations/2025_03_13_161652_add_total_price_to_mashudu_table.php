<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalPriceToMashuduTable extends Migration
{
    
    public function up()
    {
        Schema::table('mashudu', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->after('discount');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('mashudu'); 
    }
}