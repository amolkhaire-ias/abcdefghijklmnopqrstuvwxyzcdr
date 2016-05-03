<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinParameter extends Model
{
    //
    protected $table = 'finparameters';
    protected $table1 = 'finperformances';

    public static function approvePending($id) {
        $currentid = FinParameter::find($id);
        $oid = $currentid->oid;
        $oldfct = FinParameter::find($oid);
        if($oid > 0) {
            $oldfct->financialratio = $currentid->financialratio;
            $oldfct->financialratiodesc = $currentid->financialratiodesc;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }

    public static function getDescription($id){
        return self::select('financialratiodesc')->where('id',$id)->first();
    }

    public static function getFinRatioList(){
        return self::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('financialratio', 'id');

    }

}
