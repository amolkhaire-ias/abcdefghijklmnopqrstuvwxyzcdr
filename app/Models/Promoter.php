<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promoter extends Model
{
    //
    public function getDates()
    {
        return array('created_at', 'updated_at', 'deleted_at', 'endTime');
    }
    public static function approvePending($id) {
        $currentid = Promoter::find($id);
        $oid = $currentid->oid;
        $oldfct = Promoter::find($oid);
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

}
