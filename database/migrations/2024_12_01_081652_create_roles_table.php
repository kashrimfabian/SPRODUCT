<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Primary key, automatically incrementing
            $table->string('name')->unique(); // Role name, must be unique
            $table->timestamps(); // Automatically adds 'created_at' and 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
