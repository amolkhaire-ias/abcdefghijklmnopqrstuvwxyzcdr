<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoterDetail extends Model
{
    //
    protected $table= 'promoterdetails';
    public function getDates()
    {
        return array('created_at', 'updated_at', 'deleted_at', 'endTime');
    }
    public static function approvePending($id) {
        $currentid = PromoterDetail::find($id);
        $oid = $currentid->oid;
        $oldfct = PromoterDetail::find($oid);
        if($oid > 0) {
            $oldfct->name = $currentid->name;
            $oldfct->networth = $currentid->networth;
            $oldfct->networthdate = $currentid->networthdate;
            $oldfct->email = $currentid->email;
            $oldfct->contactno = $currentid->contactno;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }

}
