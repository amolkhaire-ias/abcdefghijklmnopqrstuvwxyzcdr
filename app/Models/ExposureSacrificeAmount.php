<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExposureSacrificeAmount extends Model
{
    protected $table = 'exposuresacrificeamount';

    public function createSacrifice($params){
        $expdata = self::where('companyid', $params['cmpid'])->where('refdate', $params['refdate'])->first();
        if(count($expdata) > 0){
            $expdata->companyid = $params['cmpid'];
            $expdata->refdate = $params['refdate'];
            $expdata->actual_contribution = $params['actualamnt'];
            $expdata->capex = $params['capexamnt'];
            $expdata->total = $params['tcpexamnt'];
            $expdata->save();
        }else{
            $this->companyid = $params['cmpid'];
            $this->refdate = $params['refdate'];
            $this->actual_contribution = $params['actualamnt'];
            $this->capex = $params['capexamnt'];
            $this->total = $params['tcpexamnt'];
            $this->save();
        }
    }

    public static function getData($cmpid, $date){
        return self::where('companyid', $cmpid)->where('refdate', $date)->first();
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Relationships
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
