<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    public static function listSectorById() {
        $sectors = Sector::where('active', '=', 1)
            ->orderBy('name','ASC')
            ->where('approved', '=', 1)
            ->lists('name', 'id');
        return $sectors;
    }
    public static function getAllSectorByName() {
        $mutisectors = Sector::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->select('name', 'id')
            ->orderBy('name','ASC')
            ->get();
        return $mutisectors;
    }
    public static function approvePending($id) {
        $currentid = Sector::find($id);
        $oid = $currentid->oid;
        $oldfct = Sector::find($oid);
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
