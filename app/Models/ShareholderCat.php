<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShareholderCat extends Model
{
    protected $table = 'shareholderdetails';

    public function createShareholder($shareSaveArray, $oid=0){
        $this->oid = $oid;
        $this->categoryid = $shareSaveArray['id'];
        $this->companyid = Session::get('cmpid');
        $this->shareholdernum = $shareSaveArray['shareholdernum'];
        $this->totalshares = $shareSaveArray['totalshares'];
        $this->dematerializeform = $shareSaveArray['dematerializeform'];
        $this->numofshares = $shareSaveArray['numofshares'];
        $this->percentage = $shareSaveArray['percentage'];
        $this->save();
    }

    public static function shareholderJoin(){
        $cmpid = Session::get('cmpid');
        $shareholder = DB::table('shareholdercategories')
            ->leftjoin('shareholderdetails','shareholdercategories.id','=','shareholderdetails.categoryid')
            ->where('shareholdercategories.active','=',1)
            ->where('shareholdercategories.approved','=',1)
            ->where('shareholderdetails.companyid','=',$cmpid)
            ->select('shareholdercategories.id as sid','shareholdercategories.rootid','shareholdercategories.category',
                'shareholdercategories.code','shareholderdetails.id','shareholderdetails.categoryid', 'shareholderdetails.shareholdernum',
                'shareholderdetails.totalshares', 'shareholderdetails.dematerializeform', 'shareholderdetails.numofshares',
                'shareholderdetails.percentage','shareholderdetails.approved','shareholderdetails.active')->groupby('shareholderdetails.categoryid')->get();
        return $shareholder;
    }

    public static function shareholderNonApproveJoin(){
        $cmpid = Session::get('cmpid');
        $shareholder = DB::table('shareholdercategories')
            ->leftjoin('shareholderdetails','shareholdercategories.id','=','shareholderdetails.categoryid')
            ->where('shareholdercategories.active','=',1)
            ->where('shareholdercategories.approved','=',1)
            ->where('shareholderdetails.active','=',0)
            ->where('shareholderdetails.approved','=',0)
            ->where('shareholderdetails.companyid','=',$cmpid)
            ->select('shareholdercategories.id as sid','shareholdercategories.rootid','shareholdercategories.category',
                'shareholdercategories.code','shareholderdetails.id','shareholderdetails.categoryid', 'shareholderdetails.shareholdernum',
                'shareholderdetails.totalshares', 'shareholderdetails.dematerializeform', 'shareholderdetails.numofshares',
                'shareholderdetails.percentage','shareholderdetails.approved','shareholderdetails.active')->get();
        return $shareholder;
    }

    public static function getAllData(){
        return ShareholderCat::all();
    }

    public static function updateShareholder($id, $shareSaveArray){
        $data = ShareholderCat::find($id);
        $oid = $data ? $data->oid : 0;
        if($oid > 0){
            $data->categoryid = $shareSaveArray['id'];
            $data->shareholdernum = $shareSaveArray['shareholdernum'];
            $data->totalshares = $shareSaveArray['totalshares'];
            $data->dematerializeform = $shareSaveArray['dematerializeform'];
            $data->numofshares = $shareSaveArray['numofshares'];
            $data->percentage = $shareSaveArray['percentage'];
            $data->save();
        }else{
            $shareholderCat = new ShareholderCat();
            $shareholderCat->createShareholder($shareSaveArray,$data ? $data->id : 0);
        }
    }

    public static function shareholderJoinIndex(){
        $cmpid = Session::get('cmpid');
        $shareholder = DB::table('shareholdercategories')
        ->leftjoin('shareholderdetails',function($join) use ($cmpid){
          $join->on('shareholdercategories.id','=','shareholderdetails.categoryid')
              ->where('shareholderdetails.companyid','=',$cmpid)
            ->where('shareholderdetails.active','=',1)
                ->where('shareholderdetails.approved','=',1);
        })
        ->where('shareholdercategories.active','=',1)
        ->where('shareholdercategories.approved','=',1)
        ->select('shareholdercategories.id as sid','shareholdercategories.rootid','shareholdercategories.category',
                'shareholdercategories.code','shareholderdetails.id','shareholderdetails.categoryid', 'shareholderdetails.shareholdernum',
                'shareholderdetails.totalshares', 'shareholderdetails.dematerializeform', 'shareholderdetails.numofshares',
                'shareholderdetails.percentage','shareholderdetails.approved','shareholderdetails.active','shareholderdetails.companyid')
            ->get();
//        if(count(ShareholderCat::getAllData()) > 0){
//            $shareholder->groupby('shareholderdetails.categoryid');
//        }
        return $shareholder;

    }

//        $cmpid = Session::get('cmpid');
//        $shareholder = DB::table('shareholdercategories');
//        $shareholder->leftjoin('shareholderdetails','shareholdercategories.id','=','shareholderdetails.categoryid');
//        $shareholder->where('shareholdercategories.active','=',1);
//        $shareholder->where('shareholdercategories.approved','=',1);
//        $shareholder->where('shareholderdetails.companyid','=',$cmpid);
//        $shareholder->select('shareholdercategories.id as sid','shareholdercategories.rootid','shareholdercategories.category',
//                'shareholdercategories.code','shareholderdetails.id','shareholderdetails.categoryid', 'shareholderdetails.shareholdernum',
//                'shareholderdetails.totalshares', 'shareholderdetails.dematerializeform', 'shareholderdetails.numofshares',
//                'shareholderdetails.percentage','shareholderdetails.approved','shareholderdetails.active');
//        if(count(ShareholderCat::getAllData()) > 0){
//            $shareholder->groupby('shareholderdetails.categoryid');
//        }
//        
    public static function updatePendingCategories($shareSaveArray){
        $shareholderData = ShareholderCat::find($shareSaveArray['sdid']);
        if($shareholderData){
            $shareholderData->categoryid = $shareSaveArray['id'];
            $shareholderData->shareholdernum = $shareSaveArray['shareholdernum'];
            $shareholderData->totalshares = $shareSaveArray['totalshares'];
            $shareholderData->dematerializeform = $shareSaveArray['dematerializeform'];
            $shareholderData->numofshares = $shareSaveArray['numofshares'];
            $shareholderData->percentage = $shareSaveArray['percentage'];
            $shareholderData->save();
        }
    }
    public static function rejectPendingCategories($id){
        $shareholderData = ShareholderCat::find($id);
        if($shareholderData){
            $shareholderData->active = 1;
            $shareholderData->approved = 0;
            $shareholderData->save();
        }
    }

    public static function getSumOfShare($data){
        return DB::select( DB::raw("SELECT SUM(totalshares) as totalsharesamt FROM shareholderdetails WHERE categoryid IN ($data)"));
    }
}
