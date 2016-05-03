<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lender extends Model
{
     public $table= 'lenders';

    public static function approvePending($id) {
        $currentid = Lender::find($id);
        $oid = $currentid->oid;
        $oldfct = Lender::find($oid);
        if($oid > 0) {
            $oldfct->name = $currentid->name;
            $oldfct->description = $currentid->description;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }

    public static function getActiveLenders()
    {
        return self::where('active',1)->where('approved',1)->get()->toArray();
    }
    public static function getActiveLendersIds()
    {
        return self::select('id')->where('active',1)->where('approved',1)->get()->toArray();
    }
    public function lenderdetails()
    {
        return $this->hasMany('App\Model\LenderDetail','id');
    }


//    public function institutes() {
//        return $this->hasManyThrough('App\Models\Institute','App\Models\Company','mitype','companyid');
//    }
}
