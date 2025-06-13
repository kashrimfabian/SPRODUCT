<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalSalesToAlizetiTable extends Migration
{
    public function up()
    {
        Schema::table('alizeti', function (Blueprint $table) {
            $table->decimal('total_sales', 10, 2)->default(0.00)->after('ugido'); 
        });
    }

    public function down()
    {  

        Schema::dropIfExists('alizeti');        
    }
}