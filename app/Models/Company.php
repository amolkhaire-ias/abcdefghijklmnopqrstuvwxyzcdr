<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Company extends Model
{
    public static function createCompany($companyData, $updateid = null) {
        $company = new Company();

        $updateidf = 1;
        if($updateid) {
            $company = $company->where('oid', '=', $updateid)
                ->where('active', '=', 0)
                ->where('approved', '=', 0)
                ->first();
            if(!$company) {
                $company = new Company();
                $company->oid = $updateid;
            }
        }elseif($updateid == '0') {
            $company = Company::find($companyData['updateid']);
            $updateidf = 0;
        }

        if($updateidf == 1) {
            $company->identification = $updateid ? '-'.ltrim($companyData['identification'], '-') : $companyData['identification'];
        }
        $company->name = $companyData['name'];
        $company->primarysectorid = $companyData['primarysectorid'];
        $company->primarysectorid = $companyData['primarysectorid'];
        $company->sectordesc = $companyData['sectordesc'] ? $companyData['sectordesc'] : '';
        $company->industryid = isset($companyData['industryid'])?$companyData['industryid'] : '';
        $company->categoryid = isset($companyData['categoryid']) ? $companyData['categoryid'] : '';
        $company->companytypeid = $companyData['companytypeid'];
        $company->holdingstatusid = $companyData['holdingstatusid'];
        $company->bifr = $companyData['bifr'];
        $bifrtime = strtotime($companyData['bifrdate']);
        $bifrdate = date('Y-m-d', $bifrtime);
        $company->bifrdate = $bifrdate;
        $company->coopborrower = $companyData['coopborrower'];
        $company->willfuldefaulter = $companyData['willfuldefaulter'];
        $willfuldefaultertime = strtotime($companyData['willfuldefaulterdate']);
        $willfuldefaulterdate = date('Y-m-d', $willfuldefaultertime);
        $company->willfuldefaulterdate = $willfuldefaulterdate;
        $company->willfuldefaulterdesc = $companyData['willfuldefaulterdesc'];
        $company->borrowerclassid = isset($companyData['borrowerclassid']) ? $companyData['borrowerclassid'] : '';
        $company->activityid = isset($companyData['activityid']) ? $companyData['activityid'] : '';
        $company->relationshipmgrid = $companyData['relationshipmgrid'];
        $referredtime = strtotime($companyData['referreddate']);
        $referreddate = date('Y-m-d', $referredtime);
        $company->referreddate = $referreddate;
        $company->statusid = 1;
        $company->active = '0';
        $company->approved = '0';
        $company->save();
        $cmpid = $company->id;
        if(!$updateid) {
            $company = Company::find($cmpid);
            $company->identification = $cmpid;
            $company->save();
        }
        return $cmpid;
    }

    public function getAllCompanyDetail() {
        $userid = Session::get('userid');
        $cmpdetail = $this->select('id', 'name', 'relationshipmgrid','identification')
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
//            ->where('relationshipmgrid','=',$userid)
            ->get();
        return $cmpdetail;
    }
    public static function getAllPendingCompany() {
        $pendcmps = Company::where('active', '=', 0)
            ->where('approved', '=', 0)
            ->get();
        return $pendcmps;
    }
    public static  function approvePendingCompany($cmpid) {
        $pendcmp = Company::find($cmpid);
        $oid = $pendcmp->oid;
        $oldpendcmp = Company::find($oid);
        if($oldpendcmp) {
            $oldpendcmp->name = $pendcmp->name;
            $oldpendcmp->primarysectorid = $pendcmp->primarysectorid;
            $oldpendcmp->primarysectorid = $pendcmp->primarysectorid;
            $oldpendcmp->sectordesc = $pendcmp->sectordesc;
            $oldpendcmp->industryid = $pendcmp->industryid;
            $oldpendcmp->categoryid = $pendcmp->categoryid;
            $oldpendcmp->companytypeid = $pendcmp->companytypeid;
            $oldpendcmp->holdingstatusid = $pendcmp->holdingstatusid;
            $oldpendcmp->bifr = $pendcmp->bifr;
            $oldpendcmp->bifrdate = $pendcmp->bifrdate;
            $oldpendcmp->coopborrower = $pendcmp->coopborrower;
            $oldpendcmp->willfuldefaulter = $pendcmp->willfuldefaulter;
            $oldpendcmp->willfuldefaulterdate = $pendcmp->willfuldefaulterdate;
            $oldpendcmp->willfuldefaulterdesc =  $pendcmp->willfuldefaulterdesc;
            $oldpendcmp->borrowerclassid =  $pendcmp->borrowerclassid;
            $oldpendcmp->activityid =  $pendcmp->activityid;
            $oldpendcmp->relationshipmgrid =  $pendcmp->relationshipmgrid;
            $oldpendcmp->statusid =  $pendcmp->statusid;
            $oldpendcmp->referreddate =  $pendcmp->referreddate;
            $oldpendcmp->active = '1';
            $oldpendcmp->approved = '1';
            $oldpendcmp->save();
            $pendcmp->delete();
        }else {
            $pendcmp->active = '1';
            $pendcmp->approved = '1';
            $pendcmp->save();
        }
        return $oid;
    }
    public static  function rejectPendingCompany($cmpid) {
        $rejectcmp = Company::find($cmpid);
        $rejectcmp->active = 1;
        $rejectcmp->approved = 0;
        $rejectcmp->save();
    }

    public static function getCompanyList(){
        return Company::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
    }
    public static function getCmpNameById($cmpid){
        return Company::find($cmpid)->name;
    }

    public static function getReworkReport(){
        return DB::table('companies')
            ->join('institutes', 'companies.id', '=', 'institutes.companyid')
            ->join('users', 'companies.relationshipmgrid', '=', 'users.id')
            ->join('lenders', 'institutes.mitype', '=', 'lenders.id')
            ->select('companies.id AS cid'  , 'users.fname AS uname'  ,'companies.name AS cname', 'institutes.id AS instid', 'lenders.id AS lid', 'lenders.name AS lname')
            ->get();
    }


    public static function getLiveCasesReport(){
        return DB::table('companies')
//            ->join('exposurelenderdetails', 'companies.id', '=', 'exposurelenderdetails.companyid')
            ->leftJoin('institutes', 'companies.id', '=', 'institutes.companyid')
            ->leftJoin('industries', 'industries.id', '=', 'companies.industryid')
            ->leftJoin('lenders', 'lenders.id', '=', 'institutes.mitype')
            ->select('companies.id AS cid', 'companies.name AS cname', 'industries.name AS iname','institutes.mitype'   ,'institutes.ritype' ,'lenders.name As lname','companies.statusid AS stid')
            ->whereNotIn('companies.statusid', [1,5])
            ->where('companies.active', 1)
            ->where('companies.approved', 1)
            ->get();
    }

    public static function getLiveCasesReport3(){
        return DB::table('companies')
//            ->join('exposurelenderdetails', 'companies.id', '=', 'exposurelenderdetails.companyid')
            ->leftJoin('institutes', 'companies.id', '=', 'institutes.companyid')
            ->leftJoin('industries', 'industries.id', '=', 'companies.industryid')
            ->leftJoin('lenders', 'lenders.id', '=', 'institutes.mitype')
            ->select('companies.id AS cid', 'companies.name AS cname', 'industries.name AS iname','institutes.mitype'     ,'institutes.ritype' ,'lenders.name As lname','companies.statusid AS stid')
            ->whereNotIn('companies.statusid', [1,5])
            ->where('companies.active', 1)
            ->where('companies.approved', 1)
            ->get();
    }
    public static function updateCmpStatus($cmpid,$statusid) {
        $company = Company::find($cmpid);
        $company->statusid = $statusid;
        $company->save();
    }

    public static function getLiveCasesReport8b($industry){
        $data =  DB::table('industries')
            ->leftJoin('companies', 'industries.id', '=', 'companies.industryid')
            ->select('companies.id AS cid', 'companies.name AS cname', 'industries.id AS indusid', 'industries.name AS iname')
            ->where('companies.industryid', $industry)
            ->where('companies.active', 1)
            ->where('companies.approved', 1)
            ->get();
        if(count($data)>0){
            $temp = array();
            foreach ($data as $d){
                $temp[] = ExposureDetail::getIndustryExposerTotal($d->cid);
            }
            return array_sum($temp);
        }else{
            return '';
        }
    }

    public static function getCountCasesReport8b($industry){
        $data =  DB::table('industries')
            ->leftJoin('companies', 'industries.id', '=', 'companies.industryid')
            ->select('companies.id AS cid', 'companies.name AS cname', 'industries.id AS indusid', 'industries.name AS iname')
            ->where('companies.industryid', $industry)
            ->where('companies.active', 1)
            ->where('companies.approved', 1)
            ->get();
        return count($data);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function user() {
        return $this->belongsTo('App\User','relationshipmgrid');
    }
    public function address() {
        return $this->hasMany('App\Models\AddressCmpMaps','companyid');
    }

    public function exposure() {
        return $this->hasMany('App\Models\Exposure','companyid');
    }

    public function package() {
        return $this->hasMany('App\Models\Pacakage','companyid');
    }

    public function institute() {
        return $this->hasMany('App\Models\Institute','id');
    }
}
