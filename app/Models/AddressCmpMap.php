<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AddressCmpMap extends Model
{
    protected $table = 'addresscmpmaps';
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companyid', 'id');
    }
    public static function createCompanyAddress($companyid, $cmpaddressdata, $updateid = null) {
        $addresscmpmap = new AddressCmpMap();
        if($updateid) {
            $addresscmpmap = AddressCmpMap::find($updateid);
        }
        $addresscmpmap->companyid = $companyid;
        $addresscmpmap->addresstype = $cmpaddressdata['addresstype'];
        $addresscmpmap->address1 = $cmpaddressdata['address1'];
        $addresscmpmap->countryid = $cmpaddressdata['countryid'];
        $addresscmpmap->address2 = $cmpaddressdata['address2'];
        $addresscmpmap->stateid = $cmpaddressdata['stateid'];
        $addresscmpmap->cityid = $cmpaddressdata['cityid'];
        $addresscmpmap->contactperson = $cmpaddressdata['contactperson'];
        $addresscmpmap->emailid = $cmpaddressdata['emailid'];
        $addresscmpmap->contactnumber = $cmpaddressdata['contactnumber'];
        $addresscmpmap->save();
    }
    public static function updateCmpAddress($cmpaddressdata,$addrid,$pendview) {
        $addresscmpmap = AddressCmpMap::where('oid','=',$addrid)
            ->where('active','=',0)
            ->where('approved','=',0)
            ->first();
        if(!$addresscmpmap) {
            if($pendview) {
                $addresscmpmap = AddressCmpMap::find($addrid);
            }else {
                $addresscmpmap = new AddressCmpMap();
                $addresscmpmap->oid = $addrid;
            }
        }
        $addresscmpmap->companyid = $cmpaddressdata['companyid'];
        $addresscmpmap->addresstype = $cmpaddressdata['addresstype'];
        $addresscmpmap->address1 = $cmpaddressdata['address1'];
        $addresscmpmap->countryid = $cmpaddressdata['countryid'];
        $addresscmpmap->address2 = $cmpaddressdata['address2'];
        $addresscmpmap->stateid = $cmpaddressdata['stateid'];
        $addresscmpmap->cityid = $cmpaddressdata['cityid'];
        $addresscmpmap->contactperson = $cmpaddressdata['contactperson'];
        $addresscmpmap->emailid = $cmpaddressdata['emailid'];
        $addresscmpmap->contactnumber = $cmpaddressdata['contactnumber'];
        $addresscmpmap->active = 0;
        $addresscmpmap->approved = 0;
        $addresscmpmap->save();
    }
    public static function getCompanyAddress($companyid) {
        return AddressCmpMap::where('companyid', '=' , $companyid)
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
    }
    public static function getCmpAddrById($addrid) {
       $cmpaddr = AddressCmpMap::find($addrid);
        return $cmpaddr;
    }
    public function getAllPendAddress() {
        $addrcmpmap = DB::table('addresscmpmaps')
            ->join('companies','addresscmpmaps.companyid','=','companies.id')
            ->join('users','companies.relationshipmgrid','=','users.id')
            ->select('addresscmpmaps.id','companies.name','companies.identification','users.fname','users.lname')
            ->where('addresscmpmaps.active','=',0)
            ->where('addresscmpmaps.approved','=',0)
            ->get();
        return $addrcmpmap;
    }
    public static function approvePendingAddr($addrid) {
        $addrcmpmap = AddressCmpMap::find($addrid);
        $oid = $addrcmpmap->oid;
        if($oid > 0) {
            $oldaddrcmpmap = AddressCmpMap::find($oid);
            $oldaddrcmpmap->companyid = $addrcmpmap->companyid;
            $oldaddrcmpmap->addresstype = $addrcmpmap->addresstype;
            $oldaddrcmpmap->address1 = $addrcmpmap->address1;
            $oldaddrcmpmap->countryid = $addrcmpmap->countryid;
            $oldaddrcmpmap->address2 = $addrcmpmap->address2;
            $oldaddrcmpmap->stateid = $addrcmpmap->stateid;
            $oldaddrcmpmap->cityid = $addrcmpmap->cityid;
            $oldaddrcmpmap->contactperson = $addrcmpmap->contactperson;
            $oldaddrcmpmap->emailid = $addrcmpmap->emailid;
            $oldaddrcmpmap->contactnumber = $addrcmpmap->contactnumber;
            $oldaddrcmpmap->save();
            $addrcmpmap->delete();
        }else {
            $addrcmpmap->active = 1;
            $addrcmpmap->approved = 1;
            $addrcmpmap->save();
        }
        if($addrcmpmap)
            return true;
        return false;
    }
    public static function rejectPendingAddr($addrid) {
        $addrcmpmap = AddressCmpMap::find($addrid);
        $addrcmpmap->active = 1;
        $addrcmpmap->approved = 0;
        $addrcmpmap->save();
    }
}
