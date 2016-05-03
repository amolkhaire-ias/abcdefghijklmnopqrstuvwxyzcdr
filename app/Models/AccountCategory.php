<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    //
    protected $table = 'accountcategories';
    public static function listAccountCategoriesById() {
        $accountcategories = AccountCategory::where('active', '=', 1)
            ->where('approved', '=' ,1)
            ->orderBy('name','ASC')
            ->lists('name', 'id');
        return $accountcategories;
    }
    public static function approvePending($id) {
        $currentid = AccountCategory::find($id);
        $oid = $currentid->oid;
        $oldfct = AccountCategory::find($oid);
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
