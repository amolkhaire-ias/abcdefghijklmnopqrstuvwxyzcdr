<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pocontact extends Model
{
    //
	protected $table = 'pocontacts';
	
    public function getDates()
    {
        return array('created_at', 'updated_at', 'deleted_at', 'endTime');
    }
    public static function approvePending($id) {
        $currentid = Pocontact::find($id);
        $oid = $currentid->oid;
        $oldfct = Pocontact::find($oid);
        if($oid > 0) {
            $oldfct->name = $currentid->name;
            $oldfct->designation = $currentid->designation;
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
    public function institutedetails()
    {
        return $this->hasMany('App\Model\Institutedetail','id');
    }

}
