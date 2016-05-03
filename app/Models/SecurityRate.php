<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityRate extends Model
{
    //
    protected $table = 'securityrates';
    public static function approvePending($id) {
        $currentid = SecurityRate::find($id);
        $oid = $currentid->oid;
        $oldfct = SecurityRate::find($oid);
        if($oid > 0) {
            $oldfct->fromdate = $currentid->fromdate;
            $oldfct->todate = $currentid->todate;
            $oldfct->rate = $currentid->rate;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}
