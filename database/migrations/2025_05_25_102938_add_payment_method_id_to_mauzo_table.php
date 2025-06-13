<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('mauzo', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->nullable()->after('sells_type');
            $table->foreign('payment_id')->references('payment_id')->on('payment_methods')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('mauzo');
    }
};
