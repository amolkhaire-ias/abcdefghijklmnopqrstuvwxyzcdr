<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\LenderType;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class LenderTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $lendertype = new LenderType;
        $lendertypes = $lendertype ->where('active','=',1)
            ->where('approved','=',1)->get();
        return view('lendertypes.index')
            ->with('lendertypes', $lendertypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('lendertypes.create');
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
            'name'       => 'required',
            'description'      => 'required',

        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('lendertypes/create')
                ->withErrors($validator);

        } else {
            // store
            $lendertype = new LenderType;
            $lendertype->name = Input::get('name');
            $lendertype->description = Input::get('description');
            $lendertype->active = 0;
            $lendertype->approved = 0;
            $lendertype->save();
            // redirect
            Session::flash('message', 'Lender Type created Successfully !!');
            return Redirect::to('lendertypes');
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
        $lendertype = LenderType::find($id);

        // show the view and pass the nerd to it
        return view('lendertypes.show')
            ->with('lendertypes', $lendertype);
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
        $lendertype = LenderType::find($id);

        // show the edit form and pass the nerd
        return view('lendertypes.edit')
            ->with('lendertypes', $lendertype)
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
            'name'       => 'required',
            'description'      => 'required',

        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('lendertypes/' . $id . '/edit')
                ->withErrors($validator);

        } else {

            $lendertype = LenderType::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$lendertype) {
                if($pendview) {
                    $lendertype = LenderType::find($id);
                }else {
                    $lendertype = new LenderType();
                    $lendertype->oid = $id;
                }
            }
           // $lendertype = LenderType::find($id);
            $lendertype->name       = Input::get('name');
            $lendertype->description      = Input::get('description');
            $lendertype->save();

            // redirect
            Session::flash('message', 'Lender Type updated Successfully !!');
            return Redirect::to('lendertypes');
        }
    }


    public function destroy($id)
    {
        $table = new LenderType();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = LenderType::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Lender Type deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('lendertypes');
    }

    public function getPendingLenderType()
    {
        // Show All Pending Designation

        $lendertypes = new LenderType;
        $lendertypes = $lendertypes
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('lendertypes.pending')
            ->with('lendertypes', $lendertypes);
    }
    public function postPendingLenderType($id)
    {
        LenderType::approvePending($id);
        return Redirect::to('lendertypes/pending');
    }

    public function reject($id)
    {
        $lendertypes = LenderType::find($id);
        $lendertypes->active = '1';
        $lendertypes->approved = '0';
        $lendertypes->save();
        return Redirect::to('lendertypes/pending');
    }
}
