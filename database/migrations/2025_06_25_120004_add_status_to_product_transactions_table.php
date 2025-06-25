<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('product_transactions', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('buyer_name'); 
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('product_transactions');
    }
};
