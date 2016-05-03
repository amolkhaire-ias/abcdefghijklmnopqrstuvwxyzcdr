<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\ExposureDetail;
use App\Models\Company;
use App\Models\Exposure;
use App\Models\ExposureSacrificeAmount;
use App\Models\IracStatus;
use App\Models\Lender;
use App\Models\LenderDetail;
use App\Models\Package;
use App\Models\SacrificeAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\HttpFoundation\Response;

class ExposureDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }

    public function show(){
        Redirect::to('exposuredetails')->send();
    }

    public function index()
    {
        $cmpid = Session::get('cmpid');
        $company = Company::find($cmpid);
        $packages = Package::getPackageList($cmpid);
        $lenderids = ExposureDetail::getlenderId($cmpid);
        $exposureData = ExposureDetail::getExposureLenderData();
        $lendercdr = LenderDetail::where ('companyid','=',$cmpid)->where ('active','=',1)-> where ('approved','=',1)->where('lendertype',1)->get();
        $lendernoncdr = LenderDetail::where ('companyid','=',$cmpid)->where ('active','=',1)-> where ('approved','=',1)->where('lendertype',3)-> get();
        $lendertsm = LenderDetail::where ('companyid','=',$cmpid)->where ('active','=',1)-> where ('approved','=',1)->where('lendertype',2)->get();
        $irac = IracStatus::where ('active','=',1)-> where ('approved','=',1)-> lists('status', 'id');
        $dateWiseData = Exposure::select('id','refdate')->where('companyid','=',$cmpid)->groupBy('refdate')->get();

        return view('exposuredetails.index')
            ->with('packages',$packages)
            ->with('lendercdr',$lendercdr)
            ->with('lendernoncdr',$lendernoncdr)
            ->with('lendertsm',$lendertsm)
            ->with('company',$company)
            ->with('irac',$irac)
            ->with('lenderids',$lenderids)
            ->with('dateWiseData',$dateWiseData)
            ->with('exposureData',$exposureData);
    }

    public function store(Request $request)
    {
        $params = $request->all();
        $rules = array(
             'fbtl' => 'required|numeric',
            'fbwl' => 'required|numeric',
            'nfblc' => 'required|numeric',
            'nfbbg' => 'required|numeric',
            'datefa' => 'required|date',
            'dateref' => 'required|date',
        );

        $validator = Validator::make(Input::all(), $rules);
           if ($validator->fails()) {
            return Redirect::to('exposuredetails')
                ->withInput()
                ->withErrors($validator);
        }else{
            $checklenderid = ExposureDetail::getByJoin($params);
            if(count($checklenderid) > 0){
                Session::flash('success', 'Lender already exists please select other lender');
                return Redirect::to('exposuredetails');
            }else{
                $exposure = new Exposure();
                $exposureId = $exposure->createExposure($params);
                $expDetails = new ExposureDetail();
                $expDetails->createExpDetails($params,$exposureId);
                $sacrifice = new SacrificeAmount();
                $sacrifice->createSacrificeAmount($params,$exposureId);
                $expDetails->companyid = $params['companyid'];
                return redirect('exposuredetails');
            }

        }
    }

    public function compareExposure(Request $request, $ids){
        $dates = explode(',',$ids);
        $cmpid = Session::get('cmpid');
        $data1 = Exposure::where('refdate',$dates[0])->where('companyid','=',$cmpid)->get();
        $data2 = Exposure::where('refdate',$dates[1])->where('companyid','=',$cmpid)->get();
        $total1 = ExposureDetail::getPercentage($data1);
        $total2 = ExposureDetail::getPercentage($data2);
        $exposureData1 = array();
        $exposureData2 = array();
        foreach($data1 as $a){
            $exposureData1[] = ExposureDetail::find($a->id);
        }
        foreach($data2 as $a){
            $exposureData2[] = ExposureDetail::find($a->id);
        }
        return view('exposuredetails.compare')
            ->with('totalExposure1',$total1)
            ->with('totalExposure2',$total2)
            ->with('exposureData1',$exposureData1)
            ->with('exposureData2',$exposureData2);
    }

    public function singleExposure(Request $request,$date){
        $cmpid = Session::get('cmpid');
        $exposureByDate = Exposure::getByDate($date);
        $total = ExposureDetail::getPercentage($exposureByDate);
        $lendercdr = LenderDetail::where ('companyid','=',$cmpid)->where ('active','=',1)-> where ('approved','=',1)->get();
        $irac = IracStatus::where ('active','=',1)-> where ('approved','=',1)-> lists('status', 'id');
        $exposureData = array();
        foreach($exposureByDate as $a){
            $exposureData[] = ExposureDetail::find($a['id']);
        }
        return view('exposuredetails.showexposure')
            ->with('exposureData',$exposureData)
            ->with('exposureByDate',$exposureByDate)
            ->with('date',$date)
            ->with('irac',$irac)
            ->with('lendercdr',$lendercdr)
            ->with('totalExposure',$total);
    }

    public function contibuteExposure($date){
        $cmpid = Session::get('cmpid');
        $companyName = Helpers\HelperServiceProvider::getCompanyName();
        $exposureByDate = Exposure::getByDate($date);
        $totalPercent = ExposureDetail::getPercentage(($exposureByDate));
        $contributionPercent = ExposureDetail::getPercentageByAgreeable(($exposureByDate));
        $exposureData = array();
        foreach($exposureByDate as $a){
            $exposureData[] = SacrificeAmount::find($a['id']);
        }
        $sacData = ExposureSacrificeAmount::getData($cmpid, $date);
        return view('exposuredetails.contribution')
            ->with('exposureData',$exposureData)
            ->with('date',$date)
            ->with('sacDate',$sacData)
            ->with('companyId',$cmpid)
            ->with('totalExposure',$totalPercent)
            ->with('contributionPercent',$contributionPercent)
            ->with('companyname',$companyName);
    }

    public function saveContribution(Request $request){
        $params = $request->all();
        unset($params['_token']);
        foreach($params['ids'] as $key => $id){
            SacrificeAmount::updateSacrificAmount($id, $params['sacrifice'][$key],$params['tentative'][$key]);
        }
        $exposure = new ExposureSacrificeAmount();
        $exposure->createSacrifice($params);
        return redirect('exposuredetails');
    }

    public function updateExposure($id){
        return ExposureDetail::find($id);
    }

    public function updateExposureDetails(Request $request){
        $params = $request->all();
        $rules = array(
            'fbtl' => 'required|numeric',
            'fbwl' => 'required|numeric',
            'nfblc' => 'required|numeric',
            'nfbbg' => 'required|numeric',
            'datefa' => 'required|date',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('exposuredetails/showsingleexposure/'.$params['datereference'])
                ->withInput()
                ->withErrors($validator);
        }else{
            Exposure::updateExposure($params);
            ExposureDetail::updateExposureData($params);
            return redirect('exposuredetails');
        }
    }

    public function newExposure(){
        $cmpid = Session::get('cmpid');
//        $exposureByDate = Exposure::getByDate($date);
//        $total = ExposureDetail::getPercentage($exposureByDate);
        $lendercdr = LenderDetail::where ('companyid','=',$cmpid)->where ('active','=',1)-> where ('approved','=',1)->get();
        $irac = IracStatus::where ('active','=',1)-> where ('approved','=',1)-> lists('status', 'id');
        $exposureData = array();
//        foreach($exposureByDate as $a){
//            $exposureData[] = ExposureDetail::find($a['id']);
//        }
        return view('exposuredetails.newindex')
            ->with('exposureData',$exposureData)
//            ->with('exposureByDate',$exposureByDate)
//            ->with('date',$date)
            ->with('irac',$irac)
            ->with('lendercdr',$lendercdr);
//            ->with('totalExposure',$total);
    }

	public function saveAllExposure(Request $request){
	    $params = $request->all();
	    unset($params['_token']);
	    $deteref = strtotime($params['dateref']);
	    $date2 = date('Y-m-d',$deteref);
	    foreach ($params as $key => $param){
		if(is_array($param)){
		    $datas = array_chunk($param,9);
		    foreach ($datas as $id => $data){
		        $exposureId = Exposure::createAllExposure($data,$date2);
		        $expDetails = new ExposureDetail();
		        $expDetails->createAllExpDetails($data,$exposureId);
		        $sacrifice = new SacrificeAmount();
		        $sacrifice->createAllSacrificeAmount($data,$exposureId);
		    }
		}
	    }
	    return redirect('exposuredetails');
	}
}
