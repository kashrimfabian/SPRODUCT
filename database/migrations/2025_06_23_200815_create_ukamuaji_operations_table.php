<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukamuaji', function (Blueprint $table) {
            $table->id('uk_id');                         
            $table->unsignedBigInteger('cust_ali_id'); 
            $table->foreign('cust_ali_id')->references('cust_ali_id')->on('cust_alizeti')->onDelete('cascade');            
            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->date('tarehe');  
            $table->decimal('cleaned_kg', 10, 2);    
            $table->decimal('crude_oil', 10, 2);
            $table->decimal('mashudu_kg', 10, 2); 
            $table->decimal('initial_units', 8, 2)->nullable(); 
            $table->decimal('final_units', 8, 2)->nullable();
            $table->decimal('unit_used', 8, 2)->nullable();             
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('ukamuaji');
    }
};
