<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\Lender;
use App\Models\Package;
use App\Models\Company;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class InstituteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $table = new Institute();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Institute::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Institute deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('institute');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     // load the view and pass the nerds
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $institute = new Institute;
        $institutes = $institute
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('institute.index')
            ->with('companies', $companies)
            ->with('lenders', $lenders)
            ->with('institutes', $institutes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('lendername', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
            return view('institute.create')
            ->with('packages', $packages)
        ->with('lenders', $lenders)
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
        $status = $request->all('status');
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'insttype' => 'required|string|unique:institutes|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('institute/create')
                ->withInput()
                ->withErrors($validator);
        } else {

            $institute = new Institute();
// 	approved,active, 	packageid, companyid, insttype, 	lenderid
            $institute->lenderid = Input::get('lenderid');
            $institute->packageid = Input::get('packageid');
            $institute->companyid = Input::get('companyid');
            $institute->insttype = Input::get('insttype');
            $institute->active = 0;
            $institute->approved = 0;
            $institute->save();

            // redirect
            Session::flash('success', 'Institute  Created Successfully !');
            return Redirect::to('institute');
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
        //
        $institute = Institute::find($id);

        // show the view and pass the nerd to it
        return view('institute.show')
            ->with('institute', $institute);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        $institute = Institute::find($id);
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('lendername', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        // show the edit form and pass the nerd
        return view('institute.edit')
            ->with('institute', $institute)
        ->with('packages', $packages)
        ->with('lenders', $lenders)
        ->with('pendview', $pendview)
        ->with('companies', $companies);
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
            'insttype' => 'required|string|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('institute/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $institute = Institute::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$institute) {
                if($pendview) {
                    $institute = Institute::find($id);
                }else {
                    $institute = new Institute();
                    $institute->oid = $id;
                }
            }
            //$institute = Institute::find($id);
            $institute->lenderid = Input::get('lenderid');
            $institute->packageid = Input::get('packageid');
            $institute->companyid = Input::get('companyid');
            $institute->insttype = Input::get('insttype');
            $institute->save();

            // redirect
            Session::flash('message', 'Institute updated Successfully !!');
            return Redirect::to('institute');
        }
    }
    public function getPendingInstitute()
    {
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('lendername', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $institutes = new Institute();
        $institutes = $institutes
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('institute.pending')
            ->with('institutes', $institutes)
            ->with('companies', $companies)
            ->with('lenders', $lenders);
    }
    public function postPendingInstitute($id)
    {
        // Show All Pending Designation
        Institute::approvePending($id);
        return Redirect::to('institute/pending');
    }
    public function reject($id)
    {
        $institutes = Institute::find($id);
        $institutes->active = '1';
        $institutes->approved = '0';
        $institutes->save();
        return Redirect::to('institute/pending');
    }
}
