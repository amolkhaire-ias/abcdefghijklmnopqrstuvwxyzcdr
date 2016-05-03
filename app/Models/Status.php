<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Status extends Model {

    protected $table = 'companystatus';

    public static function getStatus(){
        return self::all();
    }
}