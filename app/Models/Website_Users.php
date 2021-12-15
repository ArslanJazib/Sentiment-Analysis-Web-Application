<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website_Users extends Model
{
    protected $table = 'website_users';
    protected $primaryKey = 'web_userId';
    public $incrementing = false;
    protected $fillable=['web_userId','user_ip'];
}
