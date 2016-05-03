<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\Exposure;
use App\Models\Package;
use App\Models\Company;
use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class ExposureController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
//        $a = Exposure::find(1);
//        $d = $a->company->name;
//        dd($d);
        $exposure = new Exposure();
        $exposures = $exposure->getActiveData();
        return view('exposure.index')
            ->with('exposures', $exposures);
    }

    public function create()
    {
        $packages = Package::getPackageList();
        $companies = Company::getCompanyList();
        return view('exposure.create')
            ->with('packages',$packages)
            ->with('companies',$companies);
    }

    public function store(Request $request)
    {
        $rules = Exposure::getRules();
        $message = Exposure::getMessages();
        $validator = Validator::make(Input::all(), $rules,$message);
        if ($validator->fails()) {
            return Redirect::to('exposure/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $exposures = new Exposure();
            $exposures->exposurerev = Input::get('exposurerev');
            $exposures->companyid = Input::get('company');
            $exposures->packageid = Input::get('packageid');
            $time = strtotime(Input::get('assistancedate'));
            $date = date('Y-m-d',$time);
            $exposures->assistancedate = $date;
            $time = strtotime(Input::get('refdate'));
            $date = date('Y-m-d',$time);
            $exposures->refdate = $date;
            $exposures->cutoff = Input::get('cutoff',false);
            $exposures->active = 0;
            $exposures->approved = 0;
            $exposures->save();
            Session::flash('success', 'Exposure Created Successfully !');
            return Redirect::to('exposure');
        }
    }

    public function show($id)
    {
        $exposureData = Exposure::find($id);
        return view('exposure.show')
            ->with('exposure', $exposureData);
    }

    public function edit($id,$pendview)
    {
        $packages = Package::getPackageList();
        $companies = Company::getCompanyList();
        $exposures = Exposure::find($id);
        return view('exposure.edit')
            ->with('exposures', $exposures)
            ->with('packages', $packages)
            ->with('companies', $companies)
            ->with('pendview', $pendview);
    }

    public function update($id)
    {
        $pendview = Input::get('pendview');
        $rules = Exposure::getRules();
        $message = Exposure::getMessages();
        $validator = validator(Input::all(), $rules, $message);
        if ($validator->fails()) {
            return Redirect::to('exposure/' . $id . '/edit/'.$pendview)
                ->withErrors($validator);
        } else {
            $exposure = Exposure::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$exposure) {
                if($pendview) {
                    $exposure = Exposure::find($id);
                }else {
                    $exposure = new Exposure();
                    $exposure->oid = $id;
                }
            }
            $exposure->exposurerev = Input::get('exposurerev');
            $time = strtotime(Input::get('assistancedate'));
            $date = date('Y-m-d',$time);
            $exposure->assistancedate = $date;
            $exposure->refdate = Input::get('refdate');
            $exposure->companyid = Input::get('company');
            $exposure->packageid = Input::get('packageid');
            $exposure->cutoff = Input::get('cutoff',false);
            $exposure->save();
            Session::flash('message', 'FY Year updated Successfully !!');
            return Redirect::to('exposure');
        }
    }

    public function destroy($id)
    {
        $table = new Exposure();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Exposure::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Exposure Entry deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('exposure');
    }

    public function getPending()
    {
        $exposure = new Exposure();
        $exposures = $exposure
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();
        return view('exposure.pending')
            ->with('exposures', $exposures);
    }
    public function postPending($id)
    {
        Exposure::approvePending($id);
        return Redirect::to('exposure/pending');
    }
    public function reject($id)
    {
        $exposure = Exposure::find($id);
        $exposure->active = '1';
        $exposure->approved = '0';
        $exposure->save();
        return Redirect::to('exposure/pending');
    }
}