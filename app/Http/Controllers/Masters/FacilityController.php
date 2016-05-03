<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Facility;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FacilityController extends Controller
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
        //
        $facility = new Facility;
        $facilities = $facility
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        return view('facilities.index')
            ->with('facilities', $facilities);
    }


    public function create()
    {
        //
        return view('facilities.create');
    }


    public function store(Request $request)
    {
        //
//        $name = $request->all('name');
//        $type = $request->all('type');
//        $description = $request->old('description');

        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'type'       => 'required|Alpha|max:50',
            'description'      => 'required|string|max:255',

        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('facilities/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $facilities = new Facility;
            $facilities->name = Input::get('name');
            $facilities->type = Input::get('type');
            $facilities->description = Input::get('description');
            $facilities->active = 0;
            $facilities->approved = 0;
            $facilities->save();

            Session::flash('success', 'facilities Created Successfully !!');
            return Redirect::to('facilities');
        }
    }


    public function show($id)
    {
        //
        $facility = Facility::find($id);

        // show the view and pass the nerd to it
        return view('facilities.show')
            ->with('facilities', $facility);
    }


    public function edit($id,$pendview)
    {
        Session::put('facilityid', $id);
        $facility =Facility::find($id);
        // show the edit form and pass the nerd
        return view('facilities.edit')
            ->with('facility', $facility)
            ->with('pendview', $pendview);
    }


    public function update(Request $request, $id)
    {
        $pendview = Input::get('pendview');
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'type'       => 'required|Alpha|max:50',
            'description'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('facilities/' . $id . '/edit')
                ->withErrors($validator);

        } else {
//            $facility = Facility::find($id);
            $facilities = Facility::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
//            $oid = $facility->oid;
            if(!$facilities) {
                if($pendview) {
                    $facilities = Facility::find($id);
                }else {
                    $facilities = new Facility();
                    $facilities->oid = $id;
                }
            }
            $facilities->name       = Input::get('name');
            $facilities->type       = Input::get('type');
            $facilities->description      = Input::get('description');
            $facilities->active      = 0;
            $facilities->approved      = 0;
            $facilities->save();
            // redirect
            Session::flash('message', 'Facility updated Successfully !!');
            return Redirect::to('facilities');
        }
    }

    public function destroy($id)
    {
        $table = new Facility();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Facility::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Facilities deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('facilities');
    }

    public function getPendingFacility()
    {
        // Show All Pending Designation

        $facilities = new Facility;
        $facilities = $facilities
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('facilities.pending')
            ->with('facilities', $facilities);
    }
    public function rejectFacility($id)
    {
        $facilityid = Facility::find($id);
        $facilityid->active = '1';
        $facilityid->approved = '0';
        $facilityid->save();
        return Redirect::to('facilities/pending');
    }
    public function postPendingFacility($id)
    {
        // Show All Pending Designation

        Facility::approvePending($id);
        return Redirect::to('facilities/pending');
    }
}
