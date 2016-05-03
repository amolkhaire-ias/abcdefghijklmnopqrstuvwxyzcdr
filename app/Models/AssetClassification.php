<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetClassification extends Model
{
    protected $table = 'assetclassifications';

    public static function approvePending($id) {
        $currentid = AssetClassification::find($id);
        $oid = $currentid->oid;
        $oldfct = AssetClassification::find($oid);
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
