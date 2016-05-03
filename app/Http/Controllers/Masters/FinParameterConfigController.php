<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\FinParameterConfig;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FinParameterConfigController extends Controller
{//finparameterconfig

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
        $finparameterconfig = new FinParameterConfig;
        $finparameterconfigs = $finparameterconfig
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('finparameterconfig.index')
            ->with('finparameterconfigs', $finparameterconfigs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('finparameterconfig.create');
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
        //
        $rules = array(
            'rocepercentage' => 'required|numeric',
            'dscrfiveyears' => 'required|numeric',
            'dscrtenyears' => 'required|numeric',
            'gapirrcostfund' => 'required|numeric',
            'loanliferatio' => 'required|numeric',
            'grossprofitmargin' => 'required|numeric',
            'breakevenpoint' => 'required|numeric',
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameterconfig/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $finparameterconfig = new FinParameterConfig;
            $finparameterconfig->rocepercentage = Input::get('rocepercentage');
            $finparameterconfig->dscrfiveyears = Input::get('dscrfiveyears');
            $finparameterconfig->dscrtenyears = Input::get('dscrtenyears');
            $finparameterconfig->gapirrcostfund = Input::get('gapirrcostfund');
            $finparameterconfig->loanliferatio = Input::get('loanliferatio');
            $finparameterconfig->grossprofitmargin = Input::get('grossprofitmargin');
            $finparameterconfig->breakevenpoint = Input::get('breakevenpoint');
            $finparameterconfig->active = 0;
            $finparameterconfig->approved = 0;
            $finparameterconfig->save();

            // redirect
            Session::flash('success', 'Financial Parameter configuration Created Successfully !');
            return Redirect::to('finparameterconfig');
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
        $finparameterconfig = finparameterconfig::find($id);

        // show the view and pass the nerd to it
        return view('finparameterconfig.show')
            ->with('finparameterconfig', $finparameterconfig);
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
        $finparameterconfig = finparameterconfig::find($id);

        // show the edit form and pass the nerd
        return view('finparameterconfig.edit')
            ->with('finparameterconfig', $finparameterconfig)
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
            'rocepercentage' => 'required|numeric',
            'dscrfiveyears' => 'required|numeric',
            'dscrtenyears' => 'required|numeric',
            'gapirrcostfund' => 'required|numeric',
            'loanliferatio' => 'required|numeric',
            'grossprofitmargin' => 'required|numeric',
            'breakevenpoint' => 'required|numeric',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('finparameterconfig/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            // store
            $finparameterconfig = FinParameterConfig::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$finparameterconfig) {
                if($pendview) {
                    $finparameterconfig = FinParameterConfig::find($id);
                }else {
                    $finparameterconfig = new FinParameterConfig();
                    $finparameterconfig->oid = $id;
                }
            }

           // $finparameterconfig = FinParameterConfig::find($id);
            $finparameterconfig->rocepercentage = Input::get('rocepercentage');
            $finparameterconfig->dscrfiveyears = Input::get('dscrfiveyears');
            $finparameterconfig->dscrtenyears = Input::get('dscrtenyears');
            $finparameterconfig->gapirrcostfund = Input::get('gapirrcostfund');
            $finparameterconfig->loanliferatio = Input::get('loanliferatio');
            $finparameterconfig->grossprofitmargin = Input::get('grossprofitmargin');
            $finparameterconfig->breakevenpoint = Input::get('breakevenpoint');
            $finparameterconfig->save();
            // redirect
            Session::flash('success', 'Financial Parameter configuration Updated Successfully !');
            return Redirect::to('finparameterconfig');
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
        $table = new finparameterconfig();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = finparameterconfig::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Financial Parameter configuration deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('finparameterconfig');
    }

    public function getPendingFinParameterConfig()
    {
        // Show All Pending finparameterconfig

        $finparameterconfigs = new finparameterconfig;
        $finparameterconfigs = $finparameterconfigs
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('finparameterconfig.pending')
            ->with('finparameterconfigs', $finparameterconfigs);
    }
    public function postPendingFinParameterConfig($id)
    {
        // Show All Pending finparameterconfig
        FinParameterConfig::approvePending($id);
        return Redirect::to('finparameterconfig/pending');
    }
    public function reject($id)
    {
        $finparameterconfigs = FinParameterConfig::find($id);
        $finparameterconfigs->active = '1';
        $finparameterconfigs->approved = '0';
        $finparameterconfigs->save();
        return Redirect::to('finparameterconfig/pending');
    }
}
