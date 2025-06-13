<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_id'); 
            $table->string('name')->unique(); 
            $table->string('description')->nullable(); 
            $table->boolean('is_active')->default(true); 
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
