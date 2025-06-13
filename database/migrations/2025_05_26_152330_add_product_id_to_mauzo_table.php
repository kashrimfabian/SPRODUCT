<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('mauzo', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('sale_type');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('mauzo');
    }
};
