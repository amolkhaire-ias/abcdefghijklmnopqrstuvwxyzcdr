<?php

namespace App\Http\Controllers\Masters;

use App\Models\FinRatio;
use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\FinParameter;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FinParameterController extends Controller
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
        $finparameter = new FinParameter;
        $finparameters = $finparameter
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        return view('finparameter.index')
            ->with('finparameters', $finparameters);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('finparameter.create');
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
            'financialratio'       => 'required|string|unique:finparameters|max:255',
            'financialratiodesc'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameter/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $finparameter = new FinParameter;
            $finparameter->financialratio = Input::get('financialratio');
            $finparameter->financialratiodesc = Input::get('financialratiodesc');
            $finparameter->active = 0;
            $finparameter->approved = 0;
            $finparameter->save();

            // redirect
            Session::flash('success', 'Financial Parameter Created Successfully !');
            return Redirect::to('finparameter');
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
        $finparameter = FinParameter::find($id);
        // show the view and pass the nerd to it
        return view('finparameter.show')
            ->with('finparameter', $finparameter);
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
        $finparameter = FinParameter::find($id);

        // show the edit form and pass the nerd
        return view('finparameter.edit')
            ->with('finparameter', $finparameter)
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
            'financialratio'       => 'required',
            'financialratiodesc'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameter/' . $id . '/edit')
                ->withErrors($validator);

        } else {

            $finparameter = FinParameter::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$finparameter) {
                if($pendview) {
                    $finparameter = FinParameter::find($id);
                }else {
                    $finparameter = new FinParameter();
                    $finparameter->oid = $id;
                }
            }

            $finparameter->financialratio = Input::get('financialratio');
            $finparameter->financialratiodesc = Input::get('financialratiodesc');
            $finparameter->save();

            // redirect
            Session::flash('message', 'Financial Parameter updated Successfully !!');
            return Redirect::to('finparameter');
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
        $table = new FinParameter();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = FinParameter::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Financial Parameter deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('finparameter');
    }

    public function getPendingFinParameter()
    {
        // Show All Pending finparameter
        $finparameters = new FinParameter;
        $finparameters = $finparameters
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('finparameter.pending')
            ->with('finparameters', $finparameters);
    }
    public function postPendingFinParameter($id)
    {
        FinParameter::approvePending($id);
        return Redirect::to('finparameter/pending');
    }
    public function reject($id)
    {
        $finparameters = FinParameter::find($id);
        $finparameters->active = '1';
        $finparameters->approved = '0';
        $finparameters->save();
        return Redirect::to('finparameter/pending');
    }

    public function getFinDescription() {
        $fid = Input::get('fid');
        $finconfig = FinParameterConfig::find(1);
        $finparam = FinParameter::getDescription($fid);
        if($fid == 1){
            $dataconst = $finconfig->rocepercentage;
            $ratiovalue = $dataconst + (2/$dataconst)*100;
        }else if($fid == 2){
            $dataconst = $finconfig->dscrfiveyears;
            $ratiovalue = $dataconst + (2/$dataconst)*100;
        }else if($fid == 3){
            $dataconst = $finconfig->gapirrcostfund;
            $ratiovalue = $dataconst + (2/$dataconst)*100;
        }else if($fid == 4){
            $dataconst = $finconfig->loanliferatio;
            $ratiovalue = $dataconst + (2/$dataconst)*100;
        }else if($fid == 5){
            $ratiovalue = 'gpm';
        }else if($fid == 6){
            $dataconst = $finconfig->breakevenpoint;
            $ratiovalue = $dataconst + (2/$dataconst)*100;
        }else if($fid == 8){
            $dataconst = $finconfig->dscrtenyears;
            $ratiovalue = $dataconst + (2/$dataconst)*100;
        }
        $ajaxresponse = array(
            'finparamer' => $finparam,
            'ratiovalue' => $ratiovalue,
        );
        return $ajaxresponse;
    }
}
