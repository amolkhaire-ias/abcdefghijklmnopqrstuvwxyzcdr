<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Industry;
class IndustryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // get all the nerds
        $industry = new industry;
        $industries = $industry
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        // load the view and pass the nerds
        return view('industry.index')
            ->with('industries', $industries);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('industry.create');
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
            'name'       => 'required|string|unique:industries|max:50',
            'description'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('industry/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $industry = new industry;
            $industry->name = Input::get('name');
            $industry->description = Input::get('description');
            $industry->active = 0;
            $industry->approved = 0;
            $industry->save();

            // redirect
            Session::flash('success', 'Industry Created Successfully !');
            return Redirect::to('industry');
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
        $industry = industry::find($id);

        // show the view and pass the nerd to it
        return view('industry.show')
            ->with('industry', $industry);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id,$pendview)
    {
        // get the nerd
        $industry = industry::find($id);

        // show the edit form and pass the nerd
        return view('industry.edit')
            ->with('industry', $industry)
            ->with('pendview', $pendview);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $pendview = Input::get('pendview');
        $rules = array(
            'name'       => 'required',
            'description'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('industry/' . $id . '/edit/'.$pendview)
                ->withErrors($validator);
        } else {
            $industry = Industry::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$industry) {
                if($pendview) {
                    $industry = Industry::find($id);
                }else {
                    $industry = new Industry();
                    $industry->oid = $id;
                }
            }
            // store
            $industry->name           = Input::get('name');
            $industry->description    = Input::get('description');
            $industry->save();

            // redirect
            Session::flash('message', 'Industry updated Successfully !!');
            return Redirect::to('industry');
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
        $table = new Industry();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Industry::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Industry deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('industry');
    }

    public function getPendingIndustry()
    {
        // Show All Pending Designation

        $industry = new Industry;
        $industries = $industry
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('industry.pending')
            ->with('industries', $industries);
    }
    public function postPendingIndustry($id)
    {
        Industry::approvePending($id);
        return Redirect::to('industry/pending');
    }

    public function reject($id)
    {
        $industry = Industry::find($id);
        $industry->active = '1';
        $industry->approved = '0';
        $industry->save();
        return Redirect::to('industry/pending');
    }

}
