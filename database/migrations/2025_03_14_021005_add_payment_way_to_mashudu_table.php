<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentWayToMashuduTable extends Migration
{
    
    public function up()
    {
        Schema::table('mashudu', function (Blueprint $table) {
            $table->enum('payment_way', ['cash', 'Lipa_Namba'])->after('total_price');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('mashudu');
    }
}