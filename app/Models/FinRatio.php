<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinRatio extends Model
{
    //
    protected $table = 'finratios';

    public function createRatio($params){
        $this->financialparameterid = $params['parameterid'];
        if($params['parameterid'] == 5){
            $this->baseinfovalue = $params['basic1'];
        }else{
            $this->baseinfovalue = $params['basic'];
        }
        $this->actualvalue = $params['actual'];
        $this->doclink = $params['uploadedfile'];
        $this->companyid = $params['companyid'];
        if($params['basic'] > $params['actual']){
            $this->compliancestatus = 1;
        }else{
            $this->compliancestatus = 0;
        }
        $this->save();
    }

    public static function getAllFinRatio(){
        return self::all();
    }

    public static function checkEntry($params){
        return self::where('financialparameterid',$params['parameterid'])->where('companyid',$params['companyid'])->get();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Relationships
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function finparameteres() {
        return $this->belongsTo('App\Models\FinParameter','financialparameterid');
    }

}
