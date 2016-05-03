<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\CompanyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class CompanyTypeController extends Controller
{

    public function index()
    {
        //
        $companytype = new CompanyType;
        $companytypes = $companytype
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        return view('companytype.index')
            ->with('companytypes', $companytypes);
    }

    public function create()
    {
        //
        return view('companytype.create');
    }

    public function store(Request $request)
    {
        //
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required|string|max:255'

        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('companytype/create')
                ->withErrors($validator);

        } else {
            // store
            $companytype = new CompanyType;

            $companytype->name       = Input::get('name');
            $companytype->description      = Input::get('description');
            $companytype->active  = 0;
            $companytype->approved = 0;
            $companytype->save();

            // redirect
            Session::flash('message', 'Country created Successfully !!');
            return Redirect::to('companytype');
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
        $companytype = CompanyType::find($id);

        // show the view and pass the nerd to it
        return view('companytype.show')
            ->with('companytype', $companytype);
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
        $companytype = CompanyType::find($id);

        // show the edit form and pass the nerd
        return view('companytype.edit')
            ->with('companytype', $companytype)
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
            'description'      => 'required|string|max:255'

        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('companytype/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            $companytype = CompanyType::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$companytype) {
                if($pendview) {
                    $companytype = CompanyType::find($id);
                }else {
                    $companytype = new CompanyType();
                    $companytype->oid = $id;
                }
            }

            $companytype->name       = Input::get('name');
            $companytype->description      = Input::get('description');
            $companytype->save();

            // redirect
            Session::flash('message', 'Country updated Successfully !!');
            return Redirect::to('companytype');
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
        $table = new CompanyType();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = CompanyType::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Companytype deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('companytype');
    }
    
    public function getPendingCompanyType()
    {
        // Show All Pending Designation

        $companytype = new CompanyType;
        $companytypes = $companytype
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();
        return view('companytype.pending')
            ->with('companytypes', $companytypes);
    }
    public function postPendingCompanyType($id)
    {
        // Show All Pending Designation
        CompanyType::approvePending($id);
        return Redirect::to('companytype/pending');
    }
    public function reject($id)
    {
        $companytype = CompanyType::find($id);
        $companytype->active = '1';
        $companytype->approved = '0';
        $companytype->save();
        return Redirect::to('companytype/pending');
    }

}
