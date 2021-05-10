<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachingSessionsTable extends Migration
{
    private $tableName = 'teaching_sessions';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('session_name', 150);
            $table->unsignedBigInteger('user_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['session_name', 'user_id'], 'unq_teaching_session_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function (BluePrint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('unq_teaching_session_primary');
            $table->dropIfExists($this->tableName);
        });
    }
}
