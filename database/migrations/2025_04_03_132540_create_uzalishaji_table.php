<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uzalishaji', function (Blueprint $table) {
            $table->id('uzalishaji_id');
            $table->date('tarehe'); 
            $table->decimal('mafuta_machafu', 10, 2); 
            $table->decimal('mashudu', 10, 2); 
            $table->unsignedBigInteger('alizeti_id'); 

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');    
            
            $table->foreign('alizeti_id')->references('ali_id')->on('alizeti')->onDelete('cascade');    
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uzalishaji');
    }
};
