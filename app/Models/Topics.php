<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topics extends Model
{
    protected $table = 'topics';
    protected $primaryKey = 'topic_id';
    public $incrementing = false;
    protected $fillable=['topic_id','web_userId','topic'];
}
