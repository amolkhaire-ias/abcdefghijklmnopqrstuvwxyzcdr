<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McMember extends Model
{
    protected $table = 'mcmembers';

    public static function approvePending($id) {
        $currentid = McMember::find($id);
        $oid = $currentid->oid;
        $oldfct = McMember::find($oid);
        if($oid > 0) {
            $oldfct->lenderid = $currentid->lenderid;
            $oldfct->lendertype = $currentid->lendertype;
            $oldfct->companyid = $currentid->companyid;
            $oldfct->packageid = $currentid->packageid;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}
