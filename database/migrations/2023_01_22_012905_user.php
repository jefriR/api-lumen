<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class user extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->increments('id');

            $table->string('member_id', 10);
            $table->string('name', 50);
            $table->string('email', 50);
            $table->string('password');
            $table->integer('flag_active');
            $table->integer('flag_verif');
            $table->string('token')->nullable();
            $table->string('create_by', 3)->nullable() ;
            $table->string('modify_by', 3)->nullable();
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
        //
    }
};
