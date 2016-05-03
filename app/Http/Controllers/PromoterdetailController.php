<?php

namespace App\Http\Controllers;

use App\Models\Promoter;
use App\Models\Package;
use App\Models\Company;
use App\Models\PromoterDetail;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class PromoterdetailController extends Controller
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
        $cmpname = Session::get('cmpname');
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $promoter = new PromoterDetail();
        $promoters = $promoter
            ->where('active','=',1)
            ->where('approved','=',1)
            -> where ('companyid','=',$cmpid)
            ->get();
        // load the view and pass the nerds
        return view('promoterdetails.index')
            ->with('promoters', $promoters)
            ->with('cmpname', $cmpname)
            ->with('cmpid', $cmpid)
            ->with('packages', $packages);
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
        return view('promoterdetails.create')
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
        $rules = array(
            'name'      => 'required|regex: /^[\pL\s]+$/u|max:50',
            'email'      => 'required|email|max:100',
            'contactno'      => 'required|regex: /^[+]?[0-9]?[0-9]?\d{10}$/',
//            required|numeric|digits_between:1,10',
            'networth'      => 'required|numeric|digits_between:1,10'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('promoterdetails')
                ->withInput()
                ->withErrors($validator);
        } else {
            $companyid = Session::get('cmpid');
            $promoters = new PromoterDetail();
            $promoters->name = Input::get('name');
            $promoters->companyid = $companyid;
            $promoters->networth = Input::get('networth');
            $promoters->email = Input::get('email');
            $promoters->contactno = Input::get('contactno');
            $promoterstime = strtotime( Input::get('networthdate'));
            $promotersdate = date('Y-m-d',$promoterstime);
            $promoters->networthdate = $promotersdate;
            $promoters->active = 0;
            $promoters->approved = 0;
            $promoters->save();
            // redirect
            Session::flash('success', 'promoterdetails Created Successfully !');
            return Redirect::to('promoterdetails');
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
        $promoter = PromoterDetail::find($id);

        // show the view and pass the nerd to it
        return view('promoterdetails.show')
            ->with('promoter', $promoter);

    }


    public function edit($id,$pendview)
    {
        //
        Session::put('promoterdetails', $id);
        $promoter =PromoterDetail::find($id);
        return view('promoterdetails.edit')
            ->with('promoter', $promoter)
            ->with('pendview', $pendview);
    }


    public function update(Request $request, $id)
    {
        $pendview = Input::get('pendview');
        $cmpid = Session::get('cmpid');
        $rules = array(
            'name'      => 'required|regex: /^[\pL\s]+$/u|max:50',
            'email'      => 'required|email|max:100',
            'contactno'      => 'required|regex: /^[+]?[0-9]?[0-9]?\d{10}$/',
            'networth'      => 'required|numeric|digits_between:1,10'
        );
        $validator = validator(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('promoterdetails/'. $id .'/edit/'.$pendview)
                ->withErrors($validator);
        } else {
            $promoters = PromoterDetail::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$promoters) {
                if($pendview) {
                    $promoters = PromoterDetail::find($id);
                }else {
                    $promoters = new PromoterDetail();
                    $promoters->oid = $id;
                }
            }
            $promoters->name       = Input::get('name');
            $promoters->companyid      = $cmpid;
            $promoters->networth      = Input::get('networth');
            $promoterstime = strtotime( Input::get('networthdate'));
            $promotersdate = date('Y-m-d',$promoterstime);
            $promoters->networthdate = $promotersdate;
            $promoters->email         = Input::get('email');
            $promoters->contactno     = Input::get('contactno');
            $promoters->save();
            // redirect
            Session::flash('message', 'Promoter details updated Successfully !!');
            return Redirect::to('promoterdetails');
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
        $promoters = PromoterDetail::find($id);
        $promoters->active = 0;
        $promoters->save();
        // redirect
        Session::flash('message', 'promoterdetails deleted Successfully !!');
        return Redirect::to('promoterdetails');
    }


    public function getPendingPromoter()
    {
        // Show All Pending Designation
        $promoters = new PromoterDetail();
        $promoters = $promoters
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('promoterdetails.pending')
            ->with('promoters', $promoters);
    }
    public function postPendingPromoter($id)
    {
        // Show All Pending Designation
        PromoterDetail::approvePending($id);
        return Redirect::to('promoterdetails/pending');
    }
    public function reject($id)
    {
        $promoters = PromoterDetail::find($id);
        $promoters->active = '1';
        $promoters->approved = '0';
        $promoters->save();
        return Redirect::to('promoterdetails/pending');
    }
}
