<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->decimal('price_of_lami', 10, 2)->default(0)->after('price_of_mashudu');
            $table->decimal('price_of_ugido', 10, 2)->default(0)->after('price_of_lami');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
