<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitReason extends Model
{
    protected $table = 'exitreasons';

    public static function approvePending($id) {
        $currentid = ExitReason::find($id);
        $oid = $currentid->oid;
        $oldfct = ExitReason::find($oid);
        if($oid > 0) {
            $oldfct->exitreason = $currentid->exitreason;
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
