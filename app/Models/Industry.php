<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = 'industries';
    public static function listIndustriesById() {
        $industries = Industry::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('name','ASC')
            ->lists('name', 'id');
        return $industries;
    }

    public static function approvePending($id) {
        $currentid = Industry::find($id);
        $oid = $currentid->oid;
        $oldfct = Industry::find($oid);
        if($oid > 0) {
            $oldfct->name = $currentid->name;
            $oldfct->description = $currentid->description;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}
