<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('expense_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->date('tarehe');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->foreign('user_id','fk_expenses_users')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id','fk_expense_categories')->references('category_id')->on('categories')->onDelete('cascade');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}