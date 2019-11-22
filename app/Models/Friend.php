<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public $fillable = ['user_id', 'friend_id'];
}
