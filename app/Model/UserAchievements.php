<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAchievements extends Model
{
    protected $fillable = [
        'user_id', 'game_id', 'coins_earned', 'coins_spend'
    ];
}
