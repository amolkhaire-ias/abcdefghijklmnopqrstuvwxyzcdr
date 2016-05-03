<?php

namespace App\Http\Controllers\Masters;

use App\Models\Promoter;
use App\Models\Package;
use App\Models\Company;
use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class PromoterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $promoter = new Promoter;
        $promoters = $promoter
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('promoters.index')
            ->with('promoters', $promoters)
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
        return view('promoters.create')
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
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|Alpha|max:25',
            'description'      => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('promoters/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $promoters = new Promoter;
            $promoters->name = Input::get('name');
            $promoters->description = Input::get('description');
            $promoters->active = 0;
            $promoters->approved = 0;
            $promoters->save();
            // redirect
            Session::flash('success', 'Promoters Created Successfully !!');
            return Redirect::to('promoters');
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
        $promoter = Promoter::find($id);
        // show the view and pass the nerd to it
        return view('promoters.show')
            ->with('promoter', $promoter);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        //
        $promoter =Promoter::find($id);
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        // show the edit form and pass the nerd
        return view('promoters.edit')
            ->with('promoter', $promoter)
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
        //
        $pendview = Input::get('pendview');
        $rules = array(
            'name'       => 'required|Alpha',
            'description'      => 'required',
        );
        $validator = validator(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('promoters')
                ->withErrors($validator);

        } else {
            $promoters = Promoter::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$promoters) {
                if($pendview) {
                    $promoters = Promoter::find($id);
                }else {
                    $promoters = new Promoter();
                    $promoters->oid = $id;
                }
            }
            // store
            $promoters->name       = Input::get('name');
            $promoters->description      = Input::get('description');
            $promoters->save();
            Session::flash('message', 'Promoters updated Successfully !!');
            return Redirect::to('promoters');
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
        $table = new Promoter();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Promoter::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Performance Promoters deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('promoters');
    }


    public function getPendingPromoter()
    {
        // Show All Pending Designation
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');

        $promoters = new Promoter;
        $promoters = $promoters
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('promoters.pending')
            ->with('promoters', $promoters)
            ->with('companies', $companies);
    }
    public function postPendingPromoter($id)
    {
        Promoter::approvePending($id);
        return Redirect::to('promoters/pending');
    }
    public function reject($id)
    {
        $promoters = Promoter::find($id);
        $promoters->active = '1';
        $promoters->approved = '0';
        $promoters->save();
        return Redirect::to('promoters/pending');
    }
}
