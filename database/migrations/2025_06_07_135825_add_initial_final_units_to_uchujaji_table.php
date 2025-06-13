<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('uchujaji', function (Blueprint $table) {
            $table->decimal('initial_unit', 10, 2)->after('lami')->default(0.00); 
            $table->decimal('final_unit', 10, 2)->after('initial_unit')->default(0.00);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('uchujaji');
    }
};
