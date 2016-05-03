<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IracStatus extends Model
{
    //
    protected $table = 'iracstatus';

    public static function approvePending($id) {
        $currentid = IracStatus::find($id);
        $oid = $currentid->oid;
        $oldfct = IracStatus::find($oid);
        if($oid > 0) {
            $oldfct->status = $currentid->status;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
    public function exposureDetails()
    {
        return $this->hasMany('App\Model\ExposureDetails','id');
    }

}
