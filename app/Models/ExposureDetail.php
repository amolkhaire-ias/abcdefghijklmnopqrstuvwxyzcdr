<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Illuminate\Support\Facades\DB;

class ExposureDetail extends Model
{
    //
    protected $table = 'exposurelenderdetails';

    public static function getPercentage($exposureByDate){
        $tempArray = array();
        foreach($exposureByDate as $date){
            array_push($tempArray,$date['id']);
        }
        $temp = array();
        foreach($tempArray as $d){
            $temp[] = DB::table('exposurelenderdetails')->select('fb_tl','fb_wc','nfb_lc','nfb_bg')
                ->where('id',$d)
                ->where(function($query){
                    $query->where('lendertypeid',1)
                        ->orwhere('lendertypeid',2);
                })
                ->first();
        }
        $total = 0;
        $totalCount = 0;
        foreach($temp as $t){
            if($t){
                $totalCount++;
                $total += array_sum((array)$t);
            }
        }
        return array(
            'total' => $total,
            'totalCount' =>$totalCount
        );
    }

    public static function getPercentageByAgreeable($exposureByDate){
        $idArray = array();
        foreach($exposureByDate as $date){
            array_push($idArray,$date['id']);
        }
        $rowDataAgreeable = array();
        foreach($idArray as $d){
            $rowDataAgreeable[] = DB::table('exposurelenderdetails')->select('fb_tl','fb_wc','nfb_lc','nfb_bg')
                ->where('id',$d)
                ->where(function($query){
                    $query->where('lendertypeid',1)
                        ->orwhere('lendertypeid',2);
                })
                ->where('aggreablestatusid',1)
                ->first();
        }
        $agreeableTotal = 0;
        $countAgree = 0;
        foreach($rowDataAgreeable as $t){
            if($t){
                $countAgree++;
                $agreeableTotal += array_sum((array)$t);
            }
        }
        $nonDataAgreeable = array();
        foreach($idArray as $d){
            $nonDataAgreeable[] = DB::table('exposurelenderdetails')->select('fb_tl','fb_wc','nfb_lc','nfb_bg')
                ->where('id',$d)
                ->where(function($query){
                    $query->where('lendertypeid',1)
                        ->orwhere('lendertypeid',2);
                })
                ->where('aggreablestatusid',0)
                ->first();
        }
        $nonAgreeableTotal = 0;
        $countNonAgree = 0;
        foreach($nonDataAgreeable as $t){
            if($t){
                $countNonAgree++;
                $nonAgreeableTotal += array_sum((array)$t);
            }
        }
        return array(
            'agreeableTotal' => $agreeableTotal,
            'nonAgreeableTotal' => $nonAgreeableTotal,
            'countAgree' => $countAgree,
            'countNonAgree' => $countNonAgree,
        );
    }

    public static function getExposureLenderData(){
        return DB::table('exposurelenderdetails')
            ->leftjoin('lenders','exposurelenderdetails.lenderid','=','lenders.id')
            ->leftjoin('iracstatus','exposurelenderdetails.iracstatusid','=','iracstatus.id')
            ->get();
    }

    public function createExpDetails($params, $exposureId){
        $this->exposureid = $exposureId;
        $this->companyid = $params['companyid'];
//        $this->packageid = $params['packageid'];
        if($params['type'] == 1){
            $this->lenderid = $params['lender1'];
        }else if($params['type'] == 2){
            $this->lenderid = $params['lender2'];
        }else{
            $this->lenderid = $params['lender3'];
        }
        $this->lendertypeid = $params['type'];
        $this->fb_tl = $params['fbtl'];
        $this->fb_wc = $params['fbwl'];
        $this->nfb_lc = $params['nfblc'];
        $this->nfb_bg = $params['nfbbg'];
        $this->iracstatusid = $params['irac'];
        $this->aggreablestatusid = $params['agreeable'];
        $this->save();
    }

    public function createAllExpDetails($params, $exposureId){
        $this->exposureid = $exposureId;
        $this->companyid = Session::get('cmpid');
//        $this->packageid = $params['packageid'];
        $this->lendertypeid = $params[0];
        $this->lenderid = $params[1];
        $this->fb_tl = $params[2];
        $this->fb_wc = $params[3];
        $this->nfb_lc = $params[4];
        $this->nfb_bg = $params[5];
        $this->iracstatusid = $params[6];
        $this->aggreablestatusid = $params[7];
        $this->save();
    }

    public static function getExposureList(){
        return ExposureDetail::select('exposureid')->get();
    }
    public static function getExposerTotal($cmpid){
        $refdate = Exposure::select('refdate')->distinct()->where('companyid', $cmpid)->get()->toArray();
        $date = '';
        if($refdate){
            $date = min($refdate);
        }
        $temp = DB::table('exposures')
            ->leftJoin('exposurelenderdetails','exposures.id','=','exposurelenderdetails.exposureid')
            ->select('exposurelenderdetails.fb_tl','exposurelenderdetails.fb_wc','exposurelenderdetails.nfb_lc','exposurelenderdetails.nfb_bg')
            ->where('exposures.refdate', $date)
            ->where(function ($query) {
                $query->where('lendertypeid',1)
                    ->orwhere('lendertypeid',2);
            })

            ->where('exposures.companyid', $cmpid)
            ->get();
        $total = 0.00;
        foreach($temp as $t){
            $total += array_sum((array)$t);
        }
        return $total;
//        $temp = ExposureDetail::select('fb_tl','fb_wc','nfb_lc','nfb_bg')
//            ->where('companyid','=',$cmpid)
//            ->where(function ($query) {
//                $query->where('lendertypeid',1)
//                    ->orwhere('lendertypeid',2);
//            })
//            ->get()
//            ->toArray();
//        $total = 0;
//        foreach($temp as $t){
//            $total += array_sum($t);
//        }
//        return $total;
    }

    public static function getExposureData($date){
        $exposure = DB::table('exposurelenderdetails')
            ->where('refdate',$date)
            ->get();
        return $exposure;
    }

    public static function getlenderId($cmpid){
        $ids = self::select('lenderid')->where('companyid',$cmpid)->distinct()->get()->toArray();
        $temp = array();
        foreach($ids as $id){;
            array_push($temp,$id['lenderid']);
        }
        $idsArray = implode(',',$temp);
        return $temp;
    }

    public static function getByJoin($params){
        $datera = strtotime($params['dateref']);
        $date = date('Y-m-d',$datera);
        if($params['type'] == 1){
            $lenderid = $params['lender1'];
        }else if($params['type'] == 2){
            $lenderid = $params['lender2'];
        }else{
            $lenderid = $params['lender3'];
        }
        return DB::table('exposurelenderdetails')
            ->join('exposures','exposurelenderdetails.exposureid','=','exposures.id')
            ->select('lenderid')
            ->where('exposures.refdate', $date)
            ->where('exposures.companyid', $params['companyid'])
            ->where('exposurelenderdetails.lenderid', $lenderid)
            ->get();
    }

    public static function updateExposureData($params){
        $exposure = self::find($params['eid']);
        if($exposure){
            $exposure->fb_tl = $params['fbtl'];
            $exposure->fb_wc = $params['fbwl'];
            $exposure->nfb_lc = $params['nfblc'];
            $exposure->nfb_bg = $params['nfbbg'];
            $exposure->iracstatusid = $params['irac'];
            $exposure->aggreablestatusid = $params['agreeable'];
            $exposure->save();
        }
    }

    public static function getFirstExposerTotal($cmpid){
        $refdate = Exposure::select('refdate')->distinct()->where('companyid', $cmpid)->get()->toArray();
        $date = '';
        if($refdate){
            $date = min($refdate);
        }
        $temp = DB::table('exposures')
            ->leftJoin('exposurelenderdetails','exposures.id','=','exposurelenderdetails.exposureid')
            ->select('exposurelenderdetails.fb_tl','exposurelenderdetails.fb_wc','exposurelenderdetails.nfb_lc','exposurelenderdetails.nfb_bg')
            ->where('exposures.refdate', $date)
            ->where(function ($query) {
                $query->where('lendertypeid',1)
                    ->orwhere('lendertypeid',2);
            })
            ->where('exposures.companyid', $cmpid)
            ->get();
        $total = 0.00;
        foreach($temp as $t){
            $total += array_sum((array)$t);
        }
        return $total;
    }

    public static function getLenderExposerTotal($cmpid){
        $lenderArray = array();
        $refdate = Exposure::select('refdate')->distinct()->where('companyid', $cmpid)->get()->toArray();
        $lenderids = Lender::getActiveLendersIds();
        $date = '';
        if($refdate){
            $date = min($refdate);
        }
        foreach ($lenderids as $lenderid){
            $temp = DB::table('exposures')
                ->leftJoin('exposurelenderdetails','exposures.id','=','exposurelenderdetails.exposureid')
                ->select('exposurelenderdetails.fb_tl','exposurelenderdetails.fb_wc','exposurelenderdetails.nfb_lc','exposurelenderdetails.nfb_bg')
                ->where('exposures.refdate', $date)
                ->where(function ($query) {
                    $query->where('lendertypeid',1)
                        ->orwhere('lendertypeid',2);
                })

                ->where('exposures.companyid', $cmpid)
                ->where('exposurelenderdetails.lenderid', $lenderid['id'])
                ->get();
            $lenderArray[] = $temp;
        }

        $total = array();
        foreach($lenderArray as $array){
            if($array){
                foreach ($array as $a){
                    $total[] = array_sum((array)$a);
                }
            }else{
                $total[] = '';
            }
        }
        return $total;
    }

    public static function getIndustryExposerTotal($cmpid){
        $refdate = Exposure::select('refdate')->distinct()->where('companyid', $cmpid)->get()->toArray();
        $date = '';
        if($refdate){
            $date = min($refdate);
        }
        $temp = DB::table('exposures')
            ->leftJoin('exposurelenderdetails','exposures.id','=','exposurelenderdetails.exposureid')
            ->select('exposurelenderdetails.fb_tl','exposurelenderdetails.fb_wc','exposurelenderdetails.nfb_lc','exposurelenderdetails.nfb_bg')
            ->where('exposures.refdate', $date)
            ->where(function ($query) {
                $query->where('lendertypeid',1)
                    ->orwhere('lendertypeid',2);
            })
            ->where('exposures.companyid', $cmpid)
            ->get();
        $total = 0.00;
        foreach($temp as $t){
            $total += array_sum((array)$t);
        }
        return $total;
    }

    public static function getNonCdrExposerTotal($cmpid){
        $refdate = Exposure::select('refdate')->distinct()->where('companyid', $cmpid)->get()->toArray();
        $date = '';
        if($refdate){
            $date = min($refdate);
        }
        $temp = DB::table('exposures')
            ->leftJoin('exposurelenderdetails','exposures.id','=','exposurelenderdetails.exposureid')
            ->select('exposurelenderdetails.fb_tl','exposurelenderdetails.fb_wc','exposurelenderdetails.nfb_lc','exposurelenderdetails.nfb_bg')
            ->where('exposures.refdate', $date)
            ->where(function ($query) {
                $query->where('lendertypeid',3);
//                    ->orwhere('lendertypeid',2);
            })
            ->where('exposures.companyid', $cmpid)
            ->get();
        $total = 0.00;
        foreach($temp as $t){
            $total += array_sum((array)$t);
        }
        return $total;
    }


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Relationships
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function exposure() {
        return $this->belongsTo('App\Models\Exposure','exposureid');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company','id');
    }

    public function lender() {
        return $this->belongsTo('App\Models\Lender','lenderid');
    }

    public function iracstatus() {
        return $this->belongsTo('App\Models\IracStatus','iracstatusid');
    }

    public function lendertypes() {
        return $this->belongsTo('App\Models\LenderType','lendertypeid');
    }
}
