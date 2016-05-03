<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
class LenderDetail extends Model
{
    //
    protected $table= 'lenderdetails';
//    public function lender()
//    {
//        return $this->hasMany('App\Model\ExposureDetails','id');
//    }

    public static function approvePending($id) {
        $cmpid = Session::get('cmpid');
        $currentid = LenderDetail::find($id);
        $oid = $currentid->oid;
        $oldfct = LenderDetail::find($oid);
        if($oid > 0) {
            $oldfct->lenderid = $currentid->lenderid;
            $oldfct->lendertype = $currentid->lendertype;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
            $implementation = Implementation::where('companyid','=',$cmpid)
                ->where('lenderid', '=', $currentid->lenderid)
                ->where('active', '=', 0)
                ->where('approved', '=', 0)
                ->first();
            $implementation->active = '1';
            $implementation->approved = '1';
            $implementation->save();
        }
    }

    public function lender() {
        return $this->belongsTo('App\Models\Lender','lenderid');
    }




}
