<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ExitReason;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Session;

class ExitReasonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // get all the nerds
        $exitreason = new ExitReason;
        $exitreasons = $exitreason
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('exitreason.index')
            ->with('exitreasons', $exitreasons);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('exitreason.create');
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
            'exitreason'       => 'required|string|unique:exitreasons|max:100',
            'description'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('exitreason/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $exitreason = new ExitReason;
            $exitreason->exitreason = Input::get('exitreason');
            $exitreason->description = Input::get('description');
            $exitreason->active      = 0;
            $exitreason->approved      = 0;
            $exitreason->save();

            // redirect
            Session::flash('success', 'Exit Reason Created Successfully !');
            return Redirect::to('exitreason');
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
        $exitreason = ExitReason::find($id);

        // show the view and pass the nerd to it
        return view('exitreason.show')
            ->with('exitreason', $exitreason);
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
        $exitreason = ExitReason::find($id);
        // show the edit form and pass the nerd
        return view('exitreason.edit')
            ->with('exitreason', $exitreason)
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
            'exitreason'  => 'required',
            'description'  => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('exitreason/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            $exitreason = ExitReason::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$exitreason) {
                if($pendview) {
                    $exitreason = ExitReason::find($id);
                }else {
                    $exitreason = new ExitReason();
                    $exitreason->oid = $id;
                }
            }
//            $exitreason = ExitReason::find($id);
            $exitreason->exitreason = Input::get('exitreason');
            $exitreason->description = Input::get('description');
            $exitreason->save();

            // redirect
            Session::flash('message', 'Exit Reason updated Successfully !!');
            return Redirect::to('exitreason');
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
        $table = new ExitReason();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = ExitReason::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'ExitReason deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('exitreason');
    }

    public function getPendingExitReason()
    {
        // Show All Pending exitreason

        $exitreason = new ExitReason;
        $exitreasons = $exitreason
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('exitreason.pending')
            ->with('exitreasons', $exitreasons);
    }
    public function postPendingExitReason($id)
    {
        ExitReason::approvePending($id);
        return Redirect::to('exitreason/pending');
    }
    public function reject($id)
    {
        $exitreason = ExitReason::find($id);
        $exitreason->active = '1';
        $exitreason->approved = '0';
        $exitreason->save();
        return Redirect::to('exitreason/pending');
    }
}
