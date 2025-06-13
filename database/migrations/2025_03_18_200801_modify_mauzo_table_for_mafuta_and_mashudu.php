<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMauzoTableForMafutaAndMashudu extends Migration
{
    
    public function up(): void
    {
        Schema::table('mauzo', function (Blueprint $table) {
            $table->enum('sale_type', ['mafuta', 'mashudu'])->after('tarehe');             
            
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('mauzo');
    }
}