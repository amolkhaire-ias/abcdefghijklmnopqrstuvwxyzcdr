<?php

namespace App\Http\Controllers\Masters;

use App\Models\LenderType;
use App\Models\RelationView;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\McMember;
use App\Models\Lender;
use App\Models\Package;
use App\Models\Company;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class McMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // load the view and pass the nerds
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $lendertype = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $mcmember = new McMember;
        $mcmembers = $mcmember
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('mcmember.index')
            ->with('packages', $packages)
            ->with('lenders', $lenders)
            ->with('lendertype', $lendertype)
            ->with('companies', $companies)
            ->with('mcmembers', $mcmembers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lenders = Lender::where('active','=',1)
            ->where('approved','=',1)
            ->orderBy('name','ASC')
            ->lists('name', 'id');
        $companies = Company::where('active','=',1)
            ->where('approved','=',1)
            ->orderBy('name','ASC')
            ->lists('name', 'id');
        $lendertype = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        return view('mcmember.create')
            ->with('packages', $packages)
            ->with('lenders', $lenders)
            ->with('lendertype', $lendertype)
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
            'lendertype' => 'required|string|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('mcmember/create')
                ->withInput()
                ->withErrors($validator);
        } else {

            $mcmember = new McMember();
// 	approved,active, 	packageid, companyid, insttype, 	lenderid
            $mcmember->lenderid = Input::get('lenderid');
            $mcmember->packageid = Input::get('packageid');
            $mcmember->companyid = Input::get('companyid');
            $mcmember->lendertype = Input::get('lendertype');
            $mcmember->active = 0;
            $mcmember->approved = 0;
            $mcmember->save();
            Session::flash('success', 'Mcmember Created Successfully !');
            return Redirect::to('mcmember');
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
        $mcmember = McMember::find($id);
        return view('mcmember.show')
            ->with('mcmember', $mcmember);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        $mcmember = McMember::find($id);
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $lendertype = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        // show the edit form and pass the nerd
        return view('mcmember.edit')
            ->with('mcmember', $mcmember)
            ->with('packages', $packages)
            ->with('lenders', $lenders)
            ->with('lendertype', $lendertype)
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
            'lendertype' => 'required|string|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('mcmember/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $mcmember = McMember::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$mcmember) {
                if($pendview) {
                    $mcmember = McMember::find($id);
                }else {
                    $mcmember = new McMember();
                    $mcmember->oid = $id;
                }
            }

        $mcmember->lenderid = Input::get('lenderid');
        $mcmember->packageid = Input::get('packageid');
        $mcmember->companyid = Input::get('companyid');
        $mcmember->lendertype = Input::get('lendertype');
        $mcmember->save();
        Session::flash('message', 'Mcmember updated Successfully !!');
        return Redirect::to('mcmember');
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
        $table = new McMember();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = McMember::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'McMember deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('institute');
    }
    public function getPendingMcMember()
    {
        // Show All Pending Designation
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lenders = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $lendertype = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $mcmember = new McMember();
        $mcmembers = $mcmember
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('mcmember.pending')
            ->with('mcmembers', $mcmembers)
            ->with('packages', $packages)
            ->with('lenders', $lenders)
            ->with('lendertype', $lendertype)
            ->with('companies', $companies);
    }
    public function postPendingMcMember($id)
    {
        McMember::approvePending($id);
        return Redirect::to('mcmember/pending');
    }
    public function reject($id)
    {
        $mcmember = McMember::find($id);
        $mcmember->active = '1';
        $mcmember->approved = '0';
        $mcmember->save();
        return Redirect::to('mcmember/pending');
    }
}
