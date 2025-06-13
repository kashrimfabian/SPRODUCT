<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToRawMaterialsTable extends Migration
{
    public function up()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->timestamps(); // This will add both created_at and updated_at columns
            //$table->softDeletes(); // This adds the deleted_at column
        });
    }

    public function down()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            //$table->dropSoftDeletes(); // Drop the deleted_at column
            $table->dropTimestamps(); // Drop the created_at and updated_at columns
        });
    }
}
