<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id('stock_id');
            $table->unsignedBigInteger('alizeti_id');
            $table->decimal('mafuta_masafi')->default(0);
            $table->decimal('mashudu')->default(0);
            $table->decimal('mafuta_machafu')->default(0);
            $table->timestamps();

            $table->foreign('alizeti_id')->references('ali_id')->on('alizeti')->onDelete('cascade');
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
