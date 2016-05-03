<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class SacrificeAmount extends Model
{
    protected $table = 'sacrificeamount';

    public function createSacrificeAmount($params,$exposureId){

        if($params['type'] == 1){
            $this->lenderid = $params['lender1'];
        }else if($params['type'] == 2){
            $this->lenderid = $params['lender2'];
        }else{
            $this->lenderid = $params['lender3'];
        }
        $this->lendertype = $params['type'];
        $this->companyid = $params['companyid'];
        $this->packageid = Session::get('pkgid');
        $this->exposureid = $exposureId;
        $this->save();
    }

    public function createAllSacrificeAmount($params,$exposureId){
        $this->lendertype = $params[0];
        $this->lenderid = $params[1];
        $this->companyid = Session::get('cmpid');
        $this->packageid = Session::get('pkgid');
        $this->exposureid = $exposureId;
        $this->save();
    }

    public static function updateSacrificAmount($id, $sacrifice, $tentative){
        $data = SacrificeAmount::find($id);
        $data->sacrificeamount = $sacrifice;
        $data->tentative = $tentative;
        $data->save();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Relationships
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function lendertypes() {
        return $this->belongsTo('App\Models\LenderType','lendertype');
    }

    public function lender() {
        return $this->belongsTo('App\Models\Lender','lenderid');
    }

}
