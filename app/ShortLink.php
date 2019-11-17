<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    public $fillable = ['user_id', 'prefix'];
}
