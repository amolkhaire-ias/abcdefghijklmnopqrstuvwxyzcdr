<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceParameter extends Model
{
    protected $table = 'performanceparameters';
    public static function approvePending($id) {
        $currentid = PerformanceParameter::find($id);
        $oid = $currentid->oid;
        $oldfct = PerformanceParameter::find($oid);
        if($oid > 0) {
            $oldfct->parametername = $currentid->parametername;
            $oldfct->parameterdesc = $currentid->parameterdesc;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}
