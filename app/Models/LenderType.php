<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenderType extends Model
{
    //
    protected $table= 'lendertypes';

    public function lender()
    {
        return $this->belongsTo(lender::class, 'lendertype');
        $lenders = LenderType::with('lender')->get();
        return $lenders;
    }
    public static function approvePending($id) {
        $currentid = LenderType::find($id);
        $oid = $currentid->oid;
        $oldfct = LenderType::find($oid);
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
