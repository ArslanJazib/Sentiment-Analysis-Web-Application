<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tweets extends Model
{
    protected $table = 'tweets';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable=['id','web_userId','topic_id','twitter_userId','twitter_user_screenname','tweet_text','sentiment'];
}
