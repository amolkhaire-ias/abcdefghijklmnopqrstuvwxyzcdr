<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyExchnage extends Model {
    protected $table = 'companyexchnage';
    public static function createCompanyExchange($company, $companyid, $newexgtypeids, $oldcompanyid= null) {

        $deloldcmpexgs = CompanyExchnage::select('id')
            ->where('companyid', '=', $companyid)
            ->get();
        if($deloldcmpexgs) {
            foreach($deloldcmpexgs as $deloldcmpexg) {
                $cmpexg = CompanyExchnage::find($deloldcmpexg->id);
                $cmpexg->delete();
            }
        }
        $oldxgtypeids = CompanyExchnage::where('companyid', '=', $oldcompanyid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('exchnageid', 'exchnageid');
       $cmpexgid = CompanyExchnage::where('companyid', '=', $oldcompanyid)
           ->where('active', '=', 1)
           ->where('approved', '=', 1)
           ->lists('id', 'exchnageid');
        foreach($newexgtypeids as $newexgtypeid) {
            $companyexchnage = new CompanyExchnage();
            if($oldcompanyid) {
                $companyexchnage->oid = isset($oldxgtypeids[$newexgtypeid]) ? $cmpexgid[$newexgtypeid] : '0';

                if(isset($oldxgtypeids[$newexgtypeid])) {
                    $companyexchnage->action = 'u';
                }else {
                    $companyexchnage->action = 'c';
                }
            }
            if(!$oldcompanyid) {
                $companyexchnage->action = 'c';
            }
            $companyexchnage->companyid = $companyid;
            $companyexchnage->exchnageid = $newexgtypeid;
            if($newexgtypeid == 1) {
                $nsetime = strtotime($company['nsedate']);
                $companyexchnage->date = date('y-m-d', $nsetime);
                $companyexchnage->rate = $company['nserate'];
            }else if($newexgtypeid == 2) {
                $bsetime = strtotime($company['bsedate']);
                $companyexchnage->date = date('y-m-d', $bsetime);
                $companyexchnage->rate = $company['bserate'];
            }
            $companyexchnage->active = '0';
            $companyexchnage->approved = '0';
            $companyexchnage->save();
        }

        foreach($oldxgtypeids as $oldxgtypeid) {
            $companyexchnage = new CompanyExchnage();
            $found = 0;
            foreach($newexgtypeids as $newexgtypeid) {
                if($oldxgtypeid == $newexgtypeid) {
                    $found = 1;
                }
            }

            if($found == 0) {
                $delseccompmapid = $companyexchnage->select('id')
                    ->where('companyid', '=', $oldcompanyid)
                    ->where('exchnageid', '=', $oldxgtypeid)
                    ->where('active', '=', 1)
                    ->where('approved', '=', 1)
                    ->first();
                $companyexchnage->companyid = $companyid;
                $companyexchnage->oid = $delseccompmapid->id;
                $companyexchnage->exchnageid = $oldxgtypeid;
                $companyexchnage->action = 'd';
                $companyexchnage->active = '0';
                $companyexchnage->approved = '0';
                $companyexchnage->save();
            }

        }

        return true;
    }
    public static function approvePendingCompExg($cmpid, $cmpoid) {
        $pendcmpexgids = CompanyExchnage::select('id')
            ->where('companyid', '=', $cmpid)
            ->where('active', '=', 0)
            ->where('approved', '=', 0)
            ->get();
        foreach($pendcmpexgids as $pendcmpexgid) {
            $pendcmpexg = CompanyExchnage::find($pendcmpexgid->id);
            $oid = $pendcmpexg->oid;
            $action = $pendcmpexg->action;
            $oldcmpexg = CompanyExchnage::find($oid);
            if($oid == 0 && $action == 'c') {
                if($cmpoid > 0) {
                    $pendcmpexg->companyid = $cmpoid;
                }
                $pendcmpexg->active = '1';
                $pendcmpexg->approved = '1';
                $pendcmpexg->save();
            }else if($oldcmpexg) {
                if($oid > 0 && $action == 'd') {
//                $oldcmpexg = CompanyExchnage::find($oid);
                    $oldcmpexg->active = '0';
                    $oldcmpexg->action = '';
                    $oldcmpexg->save();
                    $pendcmpexg->delete();

                }else if($oid > 0 && $action == 'u') {
//                $oldcmpexg = CompanyExchnage::find($oid);
//                $oldcmpexg->exchnageid = $pendcmpexg->exchnageid;
                    $oldcmpexg->date = $pendcmpexg->date;
                    $oldcmpexg->rate = $pendcmpexg->rate;
                    $oldcmpexg->active = '1';
                    $oldcmpexg->approved = '1';
                    $oldcmpexg->save();
                    $pendcmpexg->delete();
                }
            }
        }
        if(isset($pendcmpexg)) {
            return true;
        }
        return false;
    }
    public static function listRateByExgid($cmpid, $pendview = null) {
        $exgtyperate = CompanyExchnage::where('companyid', '=', $cmpid)
            ->where('active', '=', $pendview ? 0 : 1)
            ->where('approved', '=', $pendview ? 0 : 1)
            ->where('action', '!=', 'd')
            ->lists('rate', 'exchnageid');
        return $exgtyperate;
    }
    public static function listDateByExgid($cmpid, $pendview = null) {
        $exgtypedate = CompanyExchnage::where('companyid', '=', $cmpid)
            ->where('active', '=', $pendview ? 0 : 1)
            ->where('approved', '=', $pendview ? 0 : 1)
            ->lists('date', 'exchnageid');
        return $exgtypedate;
    }
    public static function rejectPendingCompExg($cmpid) {
        $rejectcmpexgs = CompanyExchnage::select('id')
            ->where('companyid', '=', $cmpid)
            ->get();
        if($rejectcmpexgs) {
            foreach($rejectcmpexgs as $rejectcmpexg) {
                $cmpexg = CompanyExchnage::find($rejectcmpexg->id);
                $cmpexg->active = 1;
                $cmpexg->approved = 0;
                $cmpexg->save();
            }
        }
    }
}

