<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\FinParameter;
use App\Models\FyYear;
use App\Models\Company;
use App\Models\FinPerformance;
use App\Models\PerformanceParameter;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FinPerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $cmpid = Session::get('cmpid');
//        $finperformances = FinPerformance::all();
        $fyyears = Fyyear::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        $finparameters = PerformanceParameter::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('parametername', 'id');

        $parameterids = FinPerformance::select('parameterid')
            ->groupby('parameterid')
            ->get();
        $fyyearids = FinPerformance::select('fyyearid')
            ->groupby('fyyearid')
            ->get();
        $finperformances = [];
        foreach($fyyearids as $fyyearid) {
            $finperformance = '';
            foreach($parameterids as $parameterid) {
                $finperformance[] = FinPerformance::select('fyyearid','parameterid','projected','actual')
                    ->where('fyyearid', '=', $fyyearid['fyyearid'])
                    ->where('parameterid', '=', $parameterid['parameterid'])
                    -> where ('companyid','=',$cmpid)
                    ->get();
            }
            $finperformances[] = $finperformance;
        }

        return view('finperformance.index')
            ->with('fyyears', $fyyears)
            ->with('finparameters', $finparameters)
            ->with('finperformances', $finperformances)
            ->with('parameterid',$parameterids)
            ->with('fyyearids',$fyyearids);
    }

    public function getfinparameter()

    {

        $parameterid = Input::get('parameterid');
        $fyyearid = Input::get('fyyearid');


        $finperformances = FinPerformance::where('facilityid','=', $parameterid)
            ->where('fyyearid',$fyyearid)
            ->get();
//        dd($facility);
        return $finperformances;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('finperformance.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $cmpid = Session::get('cmpid');

        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'projected'       => 'required|numeric',
            'actual'      => 'required|numeric'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('finperformance')
                ->withInput()
                ->withErrors($validator);
        } else {

            $finperformance = new FinPerformance();
            $parameterid = Input::get('parameterid');
            $fyyearid = Input::get('fyyearid');

            $finperformance = $finperformance->where('parameterid', '=', $parameterid)
                ->where('fyyearid', '=', $fyyearid)
                ->first();
            if($finperformance == null) {
                $finperformance = new FinPerformance();
            }
            $finperformance->parameterid = $parameterid;
            $finperformance->fyyearid = $fyyearid;
            $finperformance->projected = Input::get('projected');
            $finperformance->companyid = $cmpid;
            $finperformance->actual = Input::get('actual');
//            $finperformance->active = 1;
//            $finperformance->approved = 1;
            $finperformance->save();


            Session::flash('success', 'Financial Performance created Successfully !');
            return Redirect::to('finperformance');
        }

    }

    public function show($id)
    {
        // get the nerd
        $finparameter = FinParameter::find($id);
        // show the view and pass the nerd to it
        return view('finperformance.show')
            ->with('finparameter', $finparameter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        // get the nerd
        $finparameter = FinParameter::find($id);
        // show the edit form and pass the nerd
        return view('finperformance.edit')
            ->with('finparameter', $finparameter);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $cmpid = Session::get('cmpid');
        $rules = array(
            'financialratio'       => 'required',
            'financialratiodesc'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('finperformance/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            // store
            $finparameter = FinParameter::find($id);
            $finparameter->financialratio = Input::get('financialratio');
            $finparameter->companyid =$cmpid;
            $finparameter->financialratiodesc = Input::get('financialratiodesc');
            $finparameter->save();

            // redirect
            Session::flash('message', 'Financial Parameter updated Successfully !!');
            return Redirect::to('finperformance');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // Soft delete
        $finparameter = FinParameter::find($id);
        $finparameter->active = 0;
        $finparameter->save();
        // redirect
        Session::flash('message', 'Financial Parameter deleted Successfully !!');
        return Redirect::to('finperformance');
    }

    public function getPendingFinParameter()
    {
        // Show All Pending finparameter
        $finparameters = new FinParameter;
        $finparameters = $finparameters
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('finperformance.pending')
            ->with('finparameters', $finparameters);
    }
    public function postPendingFinParameter($id)
    {
        // Show All Pending finparameter
        $finparameter = FinParameter::find($id);
        $finparameter->active = 1;
        $finparameter->approved = 1;
        $finparameter->save();
        // redirect
        return back();
    }

}
