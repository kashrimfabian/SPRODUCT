<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('uchujaji', function (Blueprint $table) {
            $table->decimal('lami',10,2)->after('ugido')->default(0.0);
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('uchujaji');
    }
};
