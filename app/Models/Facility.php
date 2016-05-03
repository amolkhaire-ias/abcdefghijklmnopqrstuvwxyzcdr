<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    //
    protected $table= 'facilities';
    public static function approvePending($id) {
        $facilityid = Facility::find($id);
        $oid = $facilityid->oid;
                if($oid > 0) {
                    $oldfct = Facility::find($oid);
                    $oldfct->name = $facilityid->name;
                    $oldfct->description = $facilityid->description;
                    $oldfct->save();
                $facilityid->delete();
                }else{
                    $facilityid->active = '1';
                    $facilityid->approved = '1';
                    $facilityid->save();
                }
    }

}
