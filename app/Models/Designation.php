<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model {
   //
    public static function approvePending($id) {
        $currentid = Designation::find($id);
        $oid = $currentid->oid;
        $oldfct = Designation::find($oid);
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