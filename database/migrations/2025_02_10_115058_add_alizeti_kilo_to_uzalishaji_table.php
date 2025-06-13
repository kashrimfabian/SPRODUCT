<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('uzalishaji', function (Blueprint $table) {
            $table->decimal('alizeti_kilo')->default(0)->after('tarehe'); // Add column after tarehe
        });
    }

    public function down()
    {
        Schema::table('uzalishaji', function (Blueprint $table) {
            $table->dropColumn('alizeti_kilo'); // Rollback changes
        });
    }
};
