<?php

namespace App\Http\Controllers;
use App\Models\Implementation;
use App\Models\LenderDetail;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Lender;
use App\Models\Company;
use App\Models\LenderType;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class LenderDetailController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cmpid = Session::get('cmpid');
        $company = Company::find($cmpid);
        $lenderid = Lender::where('active','=',1)
            ->where('approved','=',1)
            ->orderBy('name','ASC')
            ->lists('name', 'id');
        $companies = Company::where('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        $lendertypes = LenderType::where('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        $lender = new LenderDetail();
        $lenders = $lender
            ->where('active','=',1)
            ->where('approved','=',1)
            -> where ('companyid','=',$cmpid)
            ->get();

        // load the view and pass the nerds
        return view('lenderdetails.index')
            ->with('lenders', $lenders)
            ->with('company', $company)
            ->with('companies', $companies)
            ->with('lendertypes', $lendertypes)
            ->with('lenderid', $lenderid);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $lendertypes = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
//        dd($lendertypes);
       return view('lenderdetails.create')
            ->with('lendertypes', $lendertypes);

    }

    public function getlender()
    {
        $cmpid = Session::get('cmpid');
        $lenderid = Input::get('lenderid');
        $lenderid = LenderDetail::where('lenderid','=', $lenderid)
            ->where('companyid','=', $cmpid)
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        return $lenderid;
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
        $pkgid = Session::get('pkgid');

        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'lenderid' => 'required',
            'lendertype' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('lenderdetails')
                ->withInput()
                ->withErrors($validator);
        } else {
            $lenderid = Input::get('lenderid');
            $oldlender = null;
            $lender = LenderDetail::where('lenderid', '=', $lenderid)
                ->where('companyid', '=', $cmpid)
                ->where('active', '=', 0)
                ->where('approved', '=', 0)
                ->first();
            if(!$lender) {
                $lender = new LenderDetail();
                $oldlender = LenderDetail::where('lenderid', '=', $lenderid)
                    ->where('companyid', '=', $cmpid)
                    ->where('active', '=', 1)
                    ->where('approved', '=', 1)
                    ->first();
                if($oldlender) {
                    $lender->oid = $oldlender->id;
                }
            }

            $lender->lenderid = Input::get('lenderid');
            $lender->companyid = $cmpid;
            $lender->lendertype = Input::get('lendertype');
            $lender->active = 0;
            $lender->approved = 0;
            $lender->save();

            $oldimplementations = Implementation::where('lenderid', '=', $lenderid)
                ->where('companyid', '=', $cmpid)
                ->first();
            if(!$oldimplementations) {
                $implementations = new Implementation();
                $implementations->lenderid = Input::get('lenderid');
                $implementations->companyid = $cmpid;
                $implementations->packageid = $pkgid;
                $implementations->active = 0;
                $implementations->approved = 0;
                $implementations->save();
             }

            // redirect
            if(!$oldlender) {
                Session::flash('success', 'Lender details Created Successfully and Send for Approval!');
            }else {
                Session::flash('success', 'Lender details Updated Successfully and Send for Approval!');
            }

            return Redirect::to('lenderdetails');
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
        $lenders = LenderDetail::find($id);

        // show the view and pass the nerd to it
        return view('lenderdetails.show')
            ->with('lenders', $lenders);
    }

    public function edit($id,$pendview)
    {
        $lenders = LenderDetail::find($id);
        $lendername = Lender::where ('active','=',1)
            ->where('approved','=',1)
            ->orderBY('name','ASC')
            ->lists('name', 'id');
        $lendertypes = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        // show the edit form and pass the nerd
        return view('lenderdetails.edit')
            ->with('lenders', $lenders)
            ->with('lendername', $lendername)
            ->with('pendview', $pendview)
            ->with('lendertypes', $lendertypes);
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
            'lenderid' => 'required|string|max:50',
//            'lendercode' => 'required|string|max:50',
            'lendertype' => 'required|string|max:50'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('lenderdetails/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $cmpid = Session::get('cmpid');
            $pocontacts = LenderDetail::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$pocontacts) {
                if($pendview) {
                    $lender = LenderDetail::find($id);
                }else {
                    $lender = new LenderDetail();
                    $lender->oid = $id;
                }
            }
            $lender->lenderid = Input::get('lenderid');
            $lender->companyid = $cmpid ;
            $lender->lendertype = Input::get('lendertype');
            $lender->save();

            // redirect
            Session::flash('success', 'Lender details updated Successfully !!');
            return Redirect::to('lenderdetails/'.$id.'/edit/1');
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
        $lender = LenderDetail::find($id);
        $lender->active = 0;
        $lender->save();
        Session::flash('message', 'Lender details deleted Successfully !!');
        return Redirect::to('lenderdetails');
    }
    public function getPendingLender()
    {
        // Show All Pending Designation
        $lenderid = Lender::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $lendertypes = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $lender = new LenderDetail();
        $lender = $lender
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('lenderdetails.pending')
            ->with('lenders', $lender)
            ->with('lenderid', $lenderid)
            ->with('lendertypes', $lendertypes);
    }
    public function postPendingLender($id)
    {
        LenderDetail::approvePending($id);
        return Redirect::to('lenderdetails/pending');
    }
    public function reject($id)
    {
        $lender = LenderDetail::find($id);
        $lender->active = '1';
        $lender->approved = '0';
        $lender->save();
        return Redirect::to('lenderdetails/pending');
    }
}
