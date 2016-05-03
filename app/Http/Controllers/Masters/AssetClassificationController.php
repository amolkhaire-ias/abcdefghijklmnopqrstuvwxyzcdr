<?php

namespace App\Http\Controllers\Masters;

use App\Models\AssetClassification;
use App\Models\RelationView;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class AssetClassificationController extends Controller
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
        // get all the nerds
        $assetclassification = new AssetClassification;
        $assetclassifications = $assetclassification
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('assetclassification.index')
            ->with('assetclassifications', $assetclassifications);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('assetclassification.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('assetclassification/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $assetclassification = new AssetClassification;
            $assetclassification->name = Input::get('name');
            $assetclassification->description = Input::get('description');
            $assetclassification->active = 0;
            $assetclassification->approved = 0;
            $assetclassification->save();

            // redirect
            Session::flash('success', 'Asset Classification Created Successfully !');
            return Redirect::to('assetclassification');
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
        // get the nerd
        $assetclassification = AssetClassification::find($id);

        // show the view and pass the nerd to it
        return view('assetclassification.show')
            ->with('assetclassification', $assetclassification);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        // get the nerd
        $assetclassification = AssetClassification::find($id);

        // show the edit form and pass the nerd
        return view('assetclassification.edit')
            ->with('assetclassification', $assetclassification)
            ->with('pendview', $pendview);
    }


    public function update(Request $request, $id)
    {
        $pendview = Input::get('pendview');
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required|string',
        );
        $validator = validator(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('assetclassification/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            $assetclassification = AssetClassification::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$assetclassification) {
                if($pendview) {
                    $assetclassification = AssetClassification::find($id);
                }else {
                    $assetclassification = new AssetClassification();
                    $assetclassification->oid = $id;
                }
            }
            // $assetclassification = AssetClassification::find($id);
            $assetclassification->name  = Input::get('name');
            $assetclassification->description = Input::get('description');
            $assetclassification->save();

            // redirect
            Session::flash('message', 'Asset Classification updated Successfully !!');
            return Redirect::to('assetclassification');
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
        $table = new Assetclassification();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Assetclassification::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Asset Classification deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('assetclassification');
    }

    public function getPendingAssClass()
    {
        // Show All Pending Asset Classification

        $assetclassification = new AssetClassification();
        $assetclassifications = $assetclassification
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('assetclassification.pending')
            ->with('assetclassifications', $assetclassifications);
    }
    public function postPendingAssClass($id)
    {
        AssetClassification::approvePending($id);
        return Redirect::to('assetclassification/pending');
    }
    public function reject($id)
    {
        $assetclassification = AssetClassification::find($id);
        $assetclassification->active = '1';
        $assetclassification->approved = '0';
        $assetclassification->save();
        return Redirect::to('assetclassification/pending');
    }
}