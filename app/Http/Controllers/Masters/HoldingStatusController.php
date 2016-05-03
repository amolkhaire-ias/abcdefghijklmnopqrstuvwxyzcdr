<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\HoldingStatus;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class HoldingStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $holdingstatus = new HoldingStatus;
        $holdingstatus = $holdingstatus
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        return view('holdingstatus.index')
            ->with('holdingstatus', $holdingstatus);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('holdingstatus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required',

        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('holdingstatus/create')
                ->withErrors($validator);

        } else {
            // store
            $lendertype = new HoldingStatus;

            $lendertype->name = Input::get('name');
            $lendertype->description = Input::get('description');
            $lendertype->active = 0;
            $lendertype->approved = 0;
            $lendertype->save();

            // redirect
            Session::flash('message', 'Holding Status created Successfully !!');
            return Redirect::to('holdingstatus');
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
        $holdingstatus = HoldingStatus::find($id);

        // show the view and pass the nerd to it
        return view('holdingstatus.show')
            ->with('holdingstatus', $holdingstatus);
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
        $holdingstatus = HoldingStatus::find($id);

        // show the edit form and pass the nerd
        return view('holdingstatus.edit')
            ->with('holdingstatus', $holdingstatus)
            ->with('pendview', $pendview);
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
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required',

        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('holdingstatus/' . $id . '/edit')
                ->withErrors($validator);

        } else {

            $holdingstatus = HoldingStatus::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$holdingstatus) {
                if($pendview) {
                    $holdingstatus = HoldingStatus::find($id);
                }else {
                    $holdingstatus = new HoldingStatus();
                    $holdingstatus->oid = $id;
                }
            }
            // store
            $holdingstatus->name       = Input::get('name');
            $holdingstatus->description      = Input::get('description');
            $holdingstatus->save();
            // redirect
            Session::flash('message', 'Holding Status updated Successfully !!');
            return Redirect::to('holdingstatus');
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
        $table = new HoldingStatus();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = HoldingStatus::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Holding Status deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('holdingstatus');
    }

    public function getPendingHoldingStatus()
    {
        // Show All Pending Designation

        $holdingstatus = new HoldingStatus;
        $holdingstatus = $holdingstatus
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();
        return view('holdingstatus.pending')
            ->with('holdingstatus', $holdingstatus);
    }
    public function postPendingHoldingStatus($id)
    {

        HoldingStatus::approvePending($id);
        return Redirect::to('holdingstatus/pending');
    }
    public function reject($id)
    {
        $holdingstatus = HoldingStatus::find($id);
        $holdingstatus->active = '1';
        $holdingstatus->approved = '0';
        $holdingstatus->save();
        return Redirect::to('holdingstatus/pending');
    }
}
