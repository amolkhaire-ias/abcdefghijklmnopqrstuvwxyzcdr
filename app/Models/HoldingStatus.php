<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoldingStatus extends Model
{
    //
    protected $table= 'holdingstatus';
    public static function listHoldingStatusById() {
        $holdingstatus = HoldingStatus::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('name', 'id');
        return $holdingstatus;
    }

    public static function approvePending($id) {
        $currentid = HoldingStatus::find($id);
        $oid = $currentid->oid;
        $oldfct = HoldingStatus::find($oid);
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
