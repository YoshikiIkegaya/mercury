<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeParticipantCreator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_user', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::create('combinations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('creator_id');
            $table->string('participant_id');
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
        Schema::drop('combinations');
        Schema::table('plan_user', function (Blueprint $table) {
            $table->increments('id');
        });
    }
}
