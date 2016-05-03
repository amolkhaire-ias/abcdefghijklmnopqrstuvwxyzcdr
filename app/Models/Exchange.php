<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $table = 'exchanges';
    public static function getAllExchanges() {
        $exchanges = Exchange::select('exchange', 'id')
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        return $exchanges;
    }
}
