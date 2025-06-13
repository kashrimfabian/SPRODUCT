<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('total_al_kgms', 10, 2)->default(0.00)->after('alizeti_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks'); 
    }
};
