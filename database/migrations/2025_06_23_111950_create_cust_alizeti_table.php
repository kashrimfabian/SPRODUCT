<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('cust_alizeti', function (Blueprint $table) {
            $table->id('cust_ali_id');            
            $table->unsignedBigInteger('cust_id');          
           
            $table->string('batch_no')->unique();            
            
            $table->decimal('uncleaned_kg', 10, 2);             
            
            $table->date('tarehe');           
            
            $table->string('status')->default('received');
            
            $table->foreign('cust_id')->references('cust_id')->on('customers')->onDelete('cascade');
            
            $table->timestamps(); 
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('cust_alizeti');
    }
};
