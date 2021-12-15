<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('web_userId');
            $table->foreign('web_userId')->references('web_userId')->on('website_users')->onDelete('cascade');
            $table->integer('topic_id');
            $table->foreign('topic_id')->references('topic_id')->on('topics')->onDelete('cascade');
            $table->string('twitter_userId')->nullable();
            $table->string('twitter_user_screenname')->nullable();
            $table->string('tweet_text')->nullable();
            $table->string('sentiment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tweets');
    }
}
