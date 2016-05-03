<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fyyear extends Model {
   //
    public static function approvePending($id) {
        $currentid = Fyyear::find($id);
        $oid = $currentid->oid;
        $oldfct = Fyyear::find($oid);
        if($oid > 0) {
            $oldfct->name = $currentid->name;
            $oldfct->fromdate = $currentid->fromdate;
            $oldfct->todate = $currentid->todate;
            $oldfct->iscurrentyear = $currentid->iscurrentyear;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}