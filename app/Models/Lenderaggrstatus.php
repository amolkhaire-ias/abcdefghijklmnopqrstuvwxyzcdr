<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lenderaggrstatus extends Model {

    protected $table = 'lenderaggrstatus';

    public static function approvePending($id) {
        $currentid = Lenderaggrstatus::find($id);
        $oid = $currentid->oid;
        $oldfct = Lenderaggrstatus::find($oid);
        if($oid > 0) {
            $oldfct->status = $currentid->status;
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