<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToRawMaterialsTable extends Migration
{
    public function up()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->softDeletes(); // This will add the deleted_at column
        });
    }

    public function down()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropSoftDeletes(); // This will remove the deleted_at column
        });
    }
}
