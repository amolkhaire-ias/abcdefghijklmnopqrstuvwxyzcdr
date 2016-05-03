<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    public static function listActivityNameById() {
        $activities = Activity::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('name','ASC')
            ->lists('name','id');
        return $activities;
    }
}
