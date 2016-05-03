<?php

namespace App\Models;

use Grav\Common\Cache;
use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    //
    protected $table= 'companytypes';

    public static function listCompanyTypesById() {
        $companytypes = CompanyType::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('name', 'id');
        return $companytypes;
    }
    public static function approvePending($id) {
        $currentid = CompanyType::find($id);
        $oid = $currentid->oid;
        $oldfct = CompanyType::find($oid);
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
