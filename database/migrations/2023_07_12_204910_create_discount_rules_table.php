<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('type')->comment('1 = Free delivery, 2 = Flat discount, 3 = Percent Discount');
            $table->integer('discount_amount')->nullable();
            $table->date('expire_date')->nullable();
            $table->integer('status')->default(1)->comment('1 = active, 0 = InActive');
            $table->string('condition_key')->comment('1 = Total Amount, 2 = Quantity');
            $table->string('conditon_oprator')->comment('> Greater than, < Less than, == Equal to');
            $table->integer('conditon_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_rules');
    }
}
