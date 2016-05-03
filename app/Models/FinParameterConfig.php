<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinParameterConfig extends Model
{
    //
    protected $table = 'finparameterconfig';
    public static function approvePending($id) {
        $currentid = FinParameterConfig::find($id);
        $oid = $currentid->oid;
        $oldfct = FinParameterConfig::find($oid);
        if($oid > 0) {
            $oldfct->rocepercentage = $currentid->rocepercentage;
            $oldfct->dscrfiveyears = $currentid->dscrfiveyears;
            $oldfct->dscrtenyears = $currentid->dscrtenyears;
            $oldfct->gapirrcostfund = $currentid->gapirrcostfund;
            $oldfct->loanliferatio = $currentid->loanliferatio;
            $oldfct->grossprofitmargin = $currentid->grossprofitmargin;
            $oldfct->breakevenpoint = $currentid->breakevenpoint;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}
