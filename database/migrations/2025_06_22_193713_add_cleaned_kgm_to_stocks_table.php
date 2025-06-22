<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('cleaned_kgm', 10, 2)->after('total_al_kgms')->default(0.00);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
