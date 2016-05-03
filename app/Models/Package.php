<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

    public function company() {
        return $this->belongsTo('App\Models\Company','id');
    }

    public function exposure() {
        return $this->hasMany('App\Models\Exposure','packageid');
    }
    //
    public static function approvePending($id) {
        $currentid = Package::find($id);
        $oid = $currentid->oid;
        $oldfct = Package::find($oid);
        if($oid > 0) {
            $oldfct->packageid = $currentid->packageid;
            $oldfct->packagedate = $currentid->packagedate;
            $oldfct->companyid = $currentid->companyid;
            $oldfct->description = $currentid->description;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
    public static function approvePendingPkg($companyid,$cmpoid) {
        $package = Package::where('companyid','=',$companyid)
            ->first();
        if($package) {
            $oid = $package->oid;
            $oldpackage = Package::find($oid);
            if ($oid > 0) {
                $oldpackage->packagedate = $package->packagedate;
                $oldpackage->companyid = $cmpoid;
                $oldpackage->description = $package->description;
                $oldpackage->save();
                $package->delete();
            } else {
                if($cmpoid > 0) {
                    $package->companyid = $cmpoid;
                }
                $package->active = '1';
                $package->approved = '1';
                $package->save();
            }
        }
    }

    public static function createPackage($companyid, $companyData) {
//        if($companyData['packagedate']) {
            $package = new Package();
            $package->companyid = $companyid;
            $package->packagedate = !empty($companyData['packagedate']) ? date('y-m-d',strtotime($companyData['packagedate'])) : '';
            $package->active = 0;
            $package->approved = 0;
            $package->save();
//        }
    }

    public static function updatePackage($companyid, $oldcompanyid, $companyData) {
        $package = Package::where('companyid','=',$companyid)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
        if($package) {
            $package->packagedate = date('y-m-d',strtotime($companyData['packagedate']));
            $package->active = 0;
            $package->approved = 0;
            $package->save();
        }else {
            $oldpackage = Package::where('companyid','=',$oldcompanyid)
                ->where('active','=',1)
                ->where('approved','=',1)
                ->first();
            $package = new Package();
            $package->packagedate = date('y-m-d',strtotime($companyData['packagedate']));
            $package->oid = $oldpackage ? $oldpackage->id : 0;
            $package->companyid = $companyid;
            $package->active = 0;
            $package->approved = 0;
            $package->save();
        }

    }
    public static function getPkgid($companyid) {
        $pkgdata =  Package::where('companyid','=',$companyid)
            ->first();
        if($pkgdata) {
            return $pkgdata->id;
        }else {
            return 0;
        }
    }
    public static function getPkgByCmpid($companyid,$pending = null) {
        $pkgdata = Package::where('companyid','=',$companyid)
            ->where('active', '=', $pending ? 0 : 1)
            ->where('approved', '=', $pending ? 0 : 1)
            ->first();
        return $pkgdata;
    }

    public static function getPackageList($cmpId){
        return Package::where ('active','=',1)-> where ('approved','=',1)->where('companyid',$cmpId)-> lists('packageid', 'id');
    }
}
