<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Sector;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class SectorController extends Controller
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
        $sector = new Sector;
        $sectors = $sector
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('sectors.index')
            ->with('sectors', $sectors);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('sectors.create');
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
        $name = $request->all('name');
        $description = $request->old('description');


        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required|string|max:255',

        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('sectors/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $sectors = new Sector;
            $sectors->name = Input::get('name');
            $sectors->description = Input::get('description');
            $sectors->active = 0;
            $sectors->approved = 0;
            $sectors->save();

            // redirect
            Session::flash('success', 'Sectors Created Successfully !');
            return Redirect::to('sectors');
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
        $sector = Sector::find($id);

        // show the view and pass the nerd to it
        return view('sectors.show')
            ->with('sectors', $sector);

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
        $sector =Sector::find($id);
        // show the edit form and pass the nerd
        return view('sectors.edit')
            ->with('sector', $sector)
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
            return Redirect::to('sectors/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            // store
            $sectors = Sector::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$sectors) {
                if($pendview) {
                    $sectors = Sector::find($id);
                }else {
                    $sectors = new Sector();
                    $sectors->oid = $id;
                }
            }

            $sectors->name       = Input::get('name');
            $sectors->description      = Input::get('description');
            $sectors->save();
            // redirect
            Session::flash('message', 'Sector updated Successfully !!');
            return Redirect::to('sectors');
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
        $table = new Sector();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Sector::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Sectors deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('sectors');
    }

    public function getPendingSector()
    {
        // Show All Pending Designation

        $sectors = new Sector;
        $sectors = $sectors
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('sectors.pending')
            ->with('sectors', $sectors);
    }
    public function postPendingSector($id)
    {
        Sector::approvePending($id);
        return Redirect::to('sectors/pending');
    }
    public function reject($id)
    {
        $accountcategory = Sector::find($id);
        $accountcategory->active = '1';
        $accountcategory->approved = '0';
        $accountcategory->save();
        return Redirect::to('sectors/pending');
    }
}
