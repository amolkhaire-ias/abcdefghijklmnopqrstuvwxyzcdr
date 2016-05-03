<?php
namespace App;
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Implementation;
use App\Http\Controllers\Controller;
use App\Models\Lender;
use App\Models\Company;
use App\Models\Institute;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class ImplementationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }
    public function index()
    {
        $cmpid = Session::get('cmpid');
        $company = Company::find($cmpid);
        $cmpstatus = $company->statusid;
        $implementations = Implementation::where('companyid','=',$cmpid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $latestdate = Implementation::where('companyid','=',$cmpid)
            ->orderBy('comdate', 'desc')
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->first();
        $lenders = Lender::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        return view('implementation.index')
            ->with('implementations',$implementations)
            ->with('latestdate',$latestdate)
            ->with('lenders',$lenders)
            ->with('cmpstatus',$cmpstatus);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
         }
    //use for updating the implementation
    public function store(Request $request)
    {
        $cmpid = Session::get('cmpid');
        $implementations = Implementation::select('lenderid')
            ->where ('active','=',1)
            ->where ('approved','=',1)
            ->where('companyid', '=', $cmpid)->get();

        foreach ($implementations as $implementation) {
            $implementationid = Input::get('implementationid'.$implementation->lenderid);
            $implementation = Implementation::find($implementationid);
            $implementationtime = strtotime(Input::get('comdate'.$implementation->lenderid));
            $implementationdate = $implementationtime != 0 ? date('Y-m-d',$implementationtime) : '';
            $implementation->comdate = $implementationdate;
            $implementation->mrareason = Input::get('mrareason'.$implementation->lenderid)?Input::get('mrareason'.$implementation->lenderid):'';
            $implementation->save();
        }

        Session::flash('success', 'Implementation updated Successfully !!');
          return Redirect::to('implementation');

    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request)
    {
       //
    }
    public function destroy($id)
    {
        //
    }
    public function getmra()
{
    $cmpid = Session::get('cmpid');
    $implementations = Implementation::where('companyid','=',$cmpid)->get();
    $lenders = Lender::where ('active','=',1)
        ->where('approved','=',1)
        ->lists('name', 'id');
    return view('implementation.mra')
        ->with('implementations',$implementations)
        ->with('lenders',$lenders);
}
    public function postmra()
    {
        $cmpid = Session::get('cmpid');
        $implementations = Implementation::select('lenderid')
            ->where ('active','=',1)
            ->where ('approved','=',1)
            ->where('companyid', '=', $cmpid)
            ->get();
        foreach ($implementations as $implementation) {
            $implementationid = Input::get('implementationid'.$implementation->lenderid);
            $implementation = Implementation::find($implementationid);
            $mrasign = Input::get('mrasign'.$implementation->lenderid,false);
            $implementation->mrasign = $mrasign ? '1' : '0' ;
            $implementationtime = strtotime(Input::get('mradate'.$implementation->lenderid));
            $implementationdate = date('Y-m-d',$implementationtime);
            $implementation->mradate = $implementationdate;
            $implementation->mrareason = Input::get('mrareason'.$implementation->lenderid)?Input::get('mrareason'.$implementation->lenderid):'';
            $implementation->save();
        }

        Session::flash('success', 'MRA updated Successfully !!');
        return Redirect::to('implementation/mra');
        //
    }

    public function getmra1()
    {
        $cmpid = Session::get('cmpid');
        $latestdate = Implementation::orderBy('resdate', 'desc')
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->first();
        $implementations = Implementation::where('companyid','=',$cmpid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $lenders = Lender::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        return view('implementation.mra1')
            ->with('implementations',$implementations)
            ->with('latestdate',$latestdate)
            ->with('lenders',$lenders);
    }
    public function postmra1()
    {
        $cmpid = Session::get('cmpid');
        $implementations = Implementation::select('lenderid')
            ->where ('active','=',1)
            ->where ('approved','=',1)
            ->where('companyid', '=', $cmpid)
            ->get();
        foreach ($implementations as $implementation) {
            $implementationid = Input::get('implementationid'.$implementation->lenderid);
            $implementation = Implementation::find($implementationid);
            $reseffect = Input::get('reseffect'.$implementation->lenderid,false);
            $implementation->reseffect = $reseffect ? '1' : '0' ;
            $implementationtime = strtotime(Input::get('resdate'.$implementation->lenderid));
            $implementationdate = date('Y-m-d',$implementationtime);
            $implementation->resdate = $implementationdate;
            $implementation->save();
        }
        Session::flash('success', 'Restructure updated Successfully !!');
        return Redirect::to('implementation/restructure');
    }

    public function implemented()
    {
        $cmpid = Session::get('cmpid');
        $implemented = Input::get('implemented');
        if($implemented == 1) {
            $statusid = 4;
        }elseif($implemented == 0) {
            $statusid = 3;
        }
        $company = Company::find($cmpid);
        $company->statusid = $statusid;
        $company->save();
        Session::flash('success', 'Implementation Status Updated Successfully !!');
        return Redirect::to('implementation');
    }
}
