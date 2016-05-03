<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    //
    public static function approvePending($id) {
        $currentid = Institute::find($id);
        $oid = $currentid->oid;
        $oldfct = Institute::find($oid);
        if($oid > 0) {
            $oldfct->lenderid = $currentid->lenderid;
            $oldfct->packageid =$currentid->packageid;
            $oldfct->companyid = $currentid->companyid;
            $oldfct->insttype = $currentid->insttype;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }

    public static function getCompanyReport(){
        return self::select('id','mitype','companyid')->where('active',1)->where('approved',1)->get();
    }

    public function pocontacts() {
        return $this->belongsTo('App\Models\Pocontact','contactid');
    }
     public function pocontacts1() {
        return $this->belongsTo('App\Models\Pocontact','contactid1');
    }

    public function lenders() {
        return $this->belongsTo('App\Models\Lender','mitype');
    }

    public function lenderid() {
        return $this->belongsTo('App\Models\Lender','ritype');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company','companyid');
    }

}