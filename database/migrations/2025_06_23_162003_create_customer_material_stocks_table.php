<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('customer_stocks', function (Blueprint $table) {
            $table->id('stock_id');            
            $table->unsignedBigInteger('ali_id');
                        
            $table->decimal('uncleaned_kg', 10, 2)->default(0.00); 
            $table->decimal('cleaned_kg', 10, 2)->default(0.00); 
            $table->decimal('crude_oil', 10, 2)->default(0.00);     
            $table->decimal('refined_oil', 10, 2)->default(0.00);    
            $table->decimal('mashudu_kg', 10, 2)->default(0.00);                 
            $table->decimal('lami_kg', 10, 2)->default(0.00);
            $table->foreign('ali_id')->references('cust_ali_id')->on('cust_alizeti')->onDelete('cascade');
                        
            
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('customer_stocks');
    }
};
