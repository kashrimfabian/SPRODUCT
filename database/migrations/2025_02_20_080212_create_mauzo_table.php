<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMauzoTable extends Migration
{
    
    public function up(): void
    {
        Schema::create('mauzo', function (Blueprint $table) {
            $table->id('mauzo_id');
            $table->date('tarehe'); 
            $table->decimal('mafuta', 8, 2); 
            $table->decimal('price', 10, 2); 
            $table->enum('payment_way', ['cash', 'Lipa_namba']); 
            $table->decimal('discount', 8, 2)->nullable(); 
            $table->decimal('debt', 10, 2)->nullable(); 

           
            $table->unsignedBigInteger('alizeti_id'); 
            $table->unsignedBigInteger('user_id');    

            
            $table->foreign('alizeti_id')
                  ->references('alizeti_id')->on('alizeti')->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('mauzo');
    }
}
