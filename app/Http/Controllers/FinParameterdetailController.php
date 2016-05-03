<?php

namespace App\Http\Controllers;

use App\Models\FinRatio;
use Illuminate\Http\Request;
//use Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\FinParameter;
use App\Models\Fyyear;
use App\Models\Company;
use App\Models\FinPerformance;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FinParameterdetailController extends Controller
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
        $finparameters = FinParameter::getFinRatioList();
        $finRatioData = FinRatio::getAllFinRatio();
        return view('finparameterdetails.index')
            ->with('finparameters', $finparameters)
            ->with('finRatioData', $finRatioData)
            ->with('companyId',$cmpid);
    }

    public function getfinparameter()

    {


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('finparameterdetails.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'projected'       => 'required|numeric',
            'actual'      => 'required|numeric'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameterdetails')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
//        $finparameter = new FinParameter;
//        $finparameter->financialratio = Input::get('financialratio');
//        $finparameter->financialratiodesc = Input::get('financialratiodesc');
//        $finparameter->active = 1;
//        $finparameter->approved = 1;
//        $finparameter->save();

//            $facilitypackage = $facilitypackageobj->where('facilityid', '=', $facilityid)

        $finperformance = new FinPerformance();
        $finperformance->parameterid = Input::get('parameterid');
        $finperformance->fyyearid = Input::get('fyyearid');

//            if(!$finperformance) {
//                $finperformance = new FinPerformance();
//                $finperformance->parameterid = Input::get('facilityid');
//                $finperformance->fyyearid = Input::get('fyyearid');
//            }

        $finperformance->projected = Input::get('projected');
        $finperformance->actual = Input::get('actual');
//        $finperformance->active = 1;
//        $finperformance->approved = 1;
        $finperformance->save();
            Session::flash('success', 'Financial Performance created Successfully !');
            return Redirect::to('finparameterdetails');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // get the nerd
        $finparameter = FinParameter::find($id);
        // show the view and pass the nerd to it
        return view('finparameterdetails.show')
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
        return view('finparameterdetails.edit')
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

        $rules = array(
            'financialratio'       => 'required',
            'financialratiodesc'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameterdetails/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            // store
            $finparameter = FinParameter::find($id);
//            $file = array('file' => Input::file('uploadedfile'));
            $destinationPath = 'uploads';
            $extension = Input::file('uploadedfile')->getClientOriginalExtension(); // getting image extension
            $fileName = rand(11111,99999).'.'.$extension; // renameing image
            Input::file('uploadedfile')->move($destinationPath, $fileName); // uploading file to given path
            $finparameter->financialratio = Input::get('financialratio');
            $finparameter->financialratiodesc = Input::get('financialratiodesc');
//            $extension = Input::file('uploadedfile')->getClientOriginalExtension();
//            Input::file('uploadedfile')->move($destinationPath);
            $finparameter->save();

            // redirect
            Session::flash('message', 'Financial Parameter updated Successfully !!');
            return Redirect::to('finparameter');
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
        return Redirect::to('finparameterdetails');
    }

    public function getPendingFinParameter()
    {
        // Show All Pending finparameter
        $finparameters = new FinParameter;
        $finparameters = $finparameters
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('finparameterdetails.pending')
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


    public function saveFinRatio(Request $request){
        $rules = array(
//            'basic'       => 'required|numeric',
            'actual'      => 'required|numeric',
            'parameterid'      => 'required|numeric',
            'uploadedfile'      => 'required|max:10000000'

        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameterdetails')
                ->withInput()
                ->withErrors($validator);
        } else {
            if (Input::file('uploadedfile')->isValid()) {
                $params = $request->all();
                $checkEntry = FinRatio::checkEntry($params);
                if(count($checkEntry) > 0){
                    Session::flash('error', 'Ratio already exists please select other ratio');
                    return Redirect::to('finparameterdetails');
                }else{
                    $destinationPath = 'uploads';
                    $name = Input::file('uploadedfile')->getClientOriginalName();
                    $params['uploadedfile'] = $name;
                    Input::file('uploadedfile')->move($destinationPath, $name); // uploading file to given path
                    $ratios = new FinRatio();
                    $ratios->createRatio($params);
                    return redirect('finparameterdetails');
                }
            }
        }
    }
}
