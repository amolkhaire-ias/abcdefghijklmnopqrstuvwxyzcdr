<?php

namespace App\Http\Controllers;

use App\Models\Pocontact;
use App\Models\Package;
use App\Models\Company;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class PocontactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

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
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $pocontact = new Pocontact;
        $pocontacts = $pocontact
            ->where('active','=',1)
            ->where('approved','=',1)
            -> where ('companyid','=',$cmpid)
            ->get();

        // load the view and pass the nerds
        return view('pocontact.index')
            ->with('pocontacts', $pocontacts)
            ->with('company', $company)
            ->with('packages', $packages)
            ->with('companies', $companies);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        return view('pocontact.create')
            ->with('packages', $packages)
            ->with('companies', $companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cmpid = Session::get('cmpid');
        $name = $request->all('name');
//        $companyid = $request->old('companyid');

        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'email'      => 'required|email|max:100',
            'contactno'      => 'required|numeric|max:9999999999',
            'designation'      => 'required|string|max:25'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('pocontacts')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $pocontacts = new Pocontact;
            $pocontacts->name = Input::get('name');
            $pocontacts->companyid = $cmpid;
//            $pocontacts->packageid = Input::get('packageid')?Input::get('packageid'):'';
            $pocontacts->designation = Input::get('designation');
            $pocontacts->email = Input::get('email');
            $pocontacts->contactno = Input::get('contactno');
            $pocontacts->active = 0;
            $pocontacts->approved = 0;
            $pocontacts->save();
            // redirect
            Session::flash('success', 'Point of Contact Created Successfully !');
            return Redirect::to('pocontacts');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pocontact = Pocontact::find($id);
        return view('pocontact.show')
            ->with('pocontacts', $pocontact);

    }

    public function edit($id,$pendview)
    {
        $pocontact =Pocontact::find($id);
        return view('pocontact.edit')
            ->with('pendview', $pendview)
            ->with('pocontact', $pocontact);
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
        $pendview = Input::get('pendview');
        $rules = array(
            'name'       =>'required|regex: /^[\pL\s]+$/u|max:50',
            'designation'  => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('pocontacts')
                ->withErrors($validator);

        } else {
            $pocontacts = Pocontact::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$pocontacts) {
                if($pendview) {
                    $pocontacts = Pocontact::find($id);
                }else {
                    $pocontacts = new Pocontact();
                    $pocontacts->oid = $id;
                }
            }

            $pocontacts->name       = Input::get('name');
            $pocontacts->designation      = Input::get('designation');
            $pocontacts->email         = Input::get('email');
            $pocontacts->contactno     = Input::get('contactno');
            $pocontacts->save();

            // redirect
            Session::flash('message', 'Point of contacts updated Successfully !!');
            return Redirect::to('pocontacts');
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
        // Soft delete
        $pocontacts = Pocontact::find($id);
        $pocontacts->active = 0;
        $pocontacts->save();
        // redirect
        Session::flash('message', 'Point Of Contact deleted Successfully !!');
        return Redirect::to('pocontacts');
    }


    public function getPendingPocontact()
    {
        // Show All Pending Designation

        $pocontacts = new Pocontact();
        $pocontact = $pocontacts
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('pocontact.pending')
            ->with('pocontacts', $pocontact);
    }
    public function postPendingPocontact($id)
    {
        Pocontact::approvePending($id);
        return Redirect::to('pocontacts/pending');
    }
    public function reject($id)
    {
        $pocontacts = Pocontact::find($id);
        $pocontacts->active = '1';
        $pocontacts->approved = '0';
        $pocontacts->save();
        return Redirect::to('pocontacts/pending');
    }

}
