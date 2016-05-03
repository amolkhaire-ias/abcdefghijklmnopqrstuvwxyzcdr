<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityPackage;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FacilitydetailController extends Controller
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
        //
        $cmpid = Session::get('cmpid');
        $company = Company::find($cmpid);
        $facilitypackages = FacilityPackage::where('companyid','=',$cmpid)->get();
        $facilities = Facility::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
//            ->get();
        // load the view and pass the nerds
        return view('facilitydetails.index')
            ->with('facilitypackages',$facilitypackages)
            ->with('company',$company)
            ->with('facilities', $facilities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $facilitypackages = FacilityPackage::all();
        $facilities = Facility::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        return view('facilitydetails.create')
            ->with('facilitypackages',$facilitypackages)
            ->with('facilities',$facilities);
    }


    public function getfacility()
    {

        $facilityid = Input::get('facilityid');
       // $facilityid = 3;
        $facility = FacilityPackage::where('facilityid','=', $facilityid)
            ->get();
        //dd($facility);
        return $facility;
    }

    public function store()
    {
        $cmpid = Session::get('cmpid');
        $rules = array(
			'name' => 'required|unique:facilitypackages',
            'precdramount' => 'required|numeric',
            'postcdramount' => 'required|numeric',
            'postapprovalrate' => 'required|string',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('facilitydetails')
                ->withInput()
                ->withErrors($validator);
        } else {
     
            $facilitypackage = new FacilityPackage();
            $facilitypackage->name = Input::get('name');
        $facilitypackage->precdramount = Input::get('precdramount');
        $facilitypackage->companyid = $cmpid;
        $facilitypackage->postcdramount = Input::get('postcdramount');
        $facilitypackage->postapprovalrate = Input::get('postapprovalrate');
        $interestdatetime = strtotime(Input::get('interestdate'));
        $interestdatedate = date('Y-m-d', $interestdatetime);
        $facilitypackage->interestdate = $interestdatedate;
        $moratoriumperiodtime = strtotime(Input::get('moratoriumperiod'));
        $moratoriumperioddate = date('Y-m-d', $moratoriumperiodtime);
        $facilitypackage->moratoriumperiod = $moratoriumperioddate;
        $installmenttime = strtotime(Input::get('installmentdate'));
        $installmentdate = date('Y-m-d', $installmenttime);
        $facilitypackage->installmentdate = $installmentdate;
        $interestreceiptdatetime = strtotime(Input::get('interestreceiptdate'));
        $interestreceiptdatedate = date('Y-m-d', $interestreceiptdatetime);
        $facilitypackage->interestreceiptdate = $interestreceiptdatedate;
        $facilitypackage->paymentschedule = Input::get('paymentschedule');
        $facilitypackage->save();
        Session::flash('success', 'facility Created Successfully !');
        return Redirect::to('facilitydetails');
    }
    }



    public function show($id)
    {
        //
        $facility = Facility::find($id);

        // show the view and pass the nerd to it
        return view('facilitydetails.show')
            ->with('facilities', $facility);
    }


    public function edit($id)
    {
        //
        $facility =Facility::find($id);

        // show the edit form and pass the nerd
        return view('facilitydetails.edit')
            ->with('facility', $facility);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $rules = array(
            'name'       => 'required',
            'type'       => 'required',
            'description'      => 'required',

        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('facilities/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            // store
            $facilities = Facility::find($id);
            $facilities->name       = Input::get('name');
            $facilities->type       = Input::get('type');
            $facilities->description      = Input::get('description');
            $facilities->save();
            // redirect
            Session::flash('message', 'facility updated Successfully !!');
            return Redirect::to('facilitydetails');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $facilities = Facility::find($id);
        $facilities->active = 0;
        $facilities->save();
        // redirect
        Session::flash('message', 'facility deleted Successfully !!');
        return Redirect::to('facilitydetails');
    }

    public function getPendingFacility()
    {
        // Show All Pending Designation

        $facilities = new Facility;
        $facilities = $facilities
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('facilitydetails.pending')
            ->with('facilities', $facilities);
    }
    public function postPendingFacility($id)
    {
        // Show All Pending Designation

        $facility = Facility::find($id);
        $facility->active = 1;
        $facility->approved = 1;
        $facility->save();
        // redirect
        return back();
    }
}
