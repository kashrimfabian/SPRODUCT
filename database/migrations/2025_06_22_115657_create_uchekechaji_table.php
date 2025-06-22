<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('uchekechaji', function (Blueprint $table) {
            
            $table->id('uchek_id');            
            $table->date('tarehe');
            $table->unsignedBigInteger('alizeti_id'); 
            $table->unsignedBigInteger('user_id');             
            $table->decimal('uncleaned_amount', 10, 2);            
            $table->decimal('makapi_amount', 10, 2);
            $table->decimal('cleaned_amount', 10, 2);
            $table->decimal('initial_unit', 10, 2)->nullable();
            $table->decimal('final_unit', 10, 2)->nullable(); 
            $table->foreign('alizeti_id')->references('ali_id')->on('alizeti')->onDelete('cascade');                  
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');          
            $table->timestamps();
        });
    } 
    
    public function down(): void
    {
        Schema::dropIfExists('uchekechaji');
    }
};
