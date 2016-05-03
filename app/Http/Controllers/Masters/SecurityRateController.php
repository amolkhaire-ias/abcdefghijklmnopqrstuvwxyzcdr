<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use App\Models\SecurityRate;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class SecurityRateController extends Controller
{
    
    public function index()
    {
        //
        $securityrate = new SecurityRate();
        $securityrates = $securityrate
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        
        return view('securityrate.index')
            ->with('securityrates', $securityrates);
    }

    
    public function create()
    {
        //
        return view('securityrate.create');
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
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'rate'       => 'required|numeric|max:9999999999',
        );

        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('securityrate/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $securityrate = new SecurityRate();
            $time = strtotime(Input::get('fromdate'));
            $date = date('Y-m-d',$time);
            $securityrate->fromdate = $date;
            $time = strtotime(Input::get('todate'));
            $date = date('Y-m-d',$time);
            $securityrate->todate = $date;
            $securityrate->rate = Input::get('rate');
            $securityrate->active = 0;
            $securityrate->approved = 0;
            $securityrate->save();
            // redirect
            Session::flash('success', 'Security Rate Created Successfully !');
            return Redirect::to('securityrate');
        }
    }

  
    public function show($id)
    {
        //
        $securityrate = SecurityRate::find($id);
        return view('securityrate.show')
            ->with('securityrate', $securityrate);
    }

   
    public function edit($id,$pendview)
    {
        //
        $securityrate = SecurityRate::find($id);

        // show the edit form and pass the nerd
        return view('securityrate.edit')
            ->with('securityrate', $securityrate)
            ->with('pendview', $pendview);
    }

  
    public function update(Request $request, $id)
    {
        //
        $pendview = Input::get('pendview');
        $rules = array(
            'rate'       => 'required|numeric|max:999999999',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('securityrate')
                ->withErrors($validator);
        } else {
            // store
            $securityrate = SecurityRate::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$securityrate) {
                if($pendview) {
                    $securityrate = SecurityRate::find($id);
                }else {
                    $securityrate = new SecurityRate();
                    $securityrate->oid = $id;
                }
            }
            $securityrate->rate = Input::get('rate');
            $time = strtotime(Input::get('fromdate'));
            $date = date('Y-m-d',$time);
            $securityrate->fromdate = $date;
            $time = strtotime(Input::get('todate'));
            $date = date('Y-m-d',$time);
            $securityrate->todate = $date;
            $securityrate->save();
            // redirect
            Session::flash('message', 'Security rate updated Successfully  !!');
            return Redirect::to('securityrate');
        }
    }

   
    public function destroy($id)
    {
        $table = new SecurityRate();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = SecurityRate::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Security Rate deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('securityrate');
    }
    public function getPendingsecurityrates()
    {
        $securityrate = new SecurityRate();
        $securityrates = $securityrate
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('securityrate.pending')
            ->with('securityrates', $securityrates);
    }
    public function postPendingsecurityrates($id)
    {
        SecurityRate::approvePending($id);
        return Redirect::to('securityrate/pending');
    }
    public function reject($id)
    {
        $securityrate = SecurityRate::find($id);
        $securityrate->active = '1';
        $securityrate->approved = '0';
        $securityrate->save();
        return Redirect::to('securityrate/pending');
    }
}
