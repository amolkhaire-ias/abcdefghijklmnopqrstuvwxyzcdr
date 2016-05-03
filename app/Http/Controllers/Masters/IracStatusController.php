<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Session;
use App\Models\IracStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class IracStatusController extends Controller
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
        $iracstatus = new IracStatus;
        $iracstatuses = $iracstatus
            ->where('active','=',1)
            ->where('approved','=',1)
                    ->get();

        // load the view and pass the nerds
        return view('iracstatus.index')
            ->with('iracstatuses', $iracstatuses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('iracstatus.create');
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
            'status' => 'required|string|unique:iracstatus|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('iracstatus/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $iracstatus = new IracStatus();

            $iracstatus->status = Input::get('status');
            $iracstatus->active = 0;
            $iracstatus->approved = 0;
            $iracstatus->save();

            // redirect
            Session::flash('success', 'Irac Status Created Successfully !');
            return Redirect::to('iracstatus');
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
        $iracstatus = IracStatus::find($id);

        // show the view and pass the nerd to it
        return view('iracstatus.show')
            ->with('iracstatus', $iracstatus);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        $iracstatus = IracStatus::find($id);

        // show the edit form and pass the nerd
        return view('iracstatus.edit')
            ->with('iracstatus', $iracstatus)
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
            'status'       => 'required'
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('iracstatus/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            // store
            $iracstatus = IracStatus::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$iracstatus) {
                if($pendview) {
                    $iracstatus = IracStatus::find($id);
                }else {
                    $iracstatus = new IracStatus();
                    $iracstatus->oid = $id;
                }
            }
           // $iracstatus = IracStatus::find($id);
            $iracstatus->status       = Input::get('status');

            $iracstatus->save();

            // redirect
            Session::flash('message', 'Irac Status updated Successfully !!');
            return Redirect::to('iracstatus');
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
        $table = new IracStatus();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = IracStatus::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'IRAC Status deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('iracstatus');
    }
    public function getPendingIracStatus()
    {
        // Show All Pending Designation
        $iracstatuses = new IracStatus();
        $iracstatuses = $iracstatuses
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('iracstatus.pending')
            ->with('iracstatuses', $iracstatuses);
    }
    public function postPendingIracStatus($id)
    {
        IracStatus::approvePending($id);
        return Redirect::to('iracstatus/pending');
    }
    public function reject($id)
    {
        $iracstatuses = IracStatus::find($id);
        $iracstatuses->active = '1';
        $iracstatuses->approved = '0';
        $iracstatuses->save();
        return Redirect::to('iracstatus/pending');
    }
}
