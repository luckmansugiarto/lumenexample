<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachingSessionBooks extends Migration
{
    public $tableName = 'teaching_session_books';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('session_id');

            $table->foreign('book_id')->references('id')->on('books');
            $table->foreign('session_id')->references('id')->on('teaching_sessions');

            $table->unique(['book_id', 'session_id'], 'unq_teaching_session_books_primary');
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
            $table->dropForeign(['book_id']);
            $table->dropForeign(['session_id']);
            $table->dropUnique('unq_teaching_session_books_primary');
            $table->dropIfExists($this->tableName);
        });
    }
}
