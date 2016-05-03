<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowerClass extends Model
{
    //
    protected $table = 'borrowerclasses';
    public static function listBorrowerClassById() {
        $borrowerclassesname = BorrowerClass::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('name','ASC')
            ->lists('name', 'id');
        return $borrowerclassesname;
    }
    public static function listBorrowerClassDescById() {
        $borrowerclassesdesc = BorrowerClass::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('description', 'id');
        return $borrowerclassesdesc;
    }
    public static function approvePending($id) {
        $currentid = BorrowerClass::find($id);
        $oid = $currentid->oid;
        $oldfct = BorrowerClass::find($oid);
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
