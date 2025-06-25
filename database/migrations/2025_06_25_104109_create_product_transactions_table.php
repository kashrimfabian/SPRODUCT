<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->id('trans_id');             
            $table->unsignedBigInteger('cust_ali_id'); 
            $table->foreign('cust_ali_id')->references('cust_ali_id')->on('cust_alizeti')->onDelete('cascade');            
            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->date('tarehe');             
            $table->string('trans_type');             
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');            
            $table->decimal('quantity', 10, 2);             
            $table->decimal('amount', 10, 2)->default(0.00);             
            $table->string('buyer_name')->nullable();                         
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('product_transactions');
    }
};
