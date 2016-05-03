<?php

namespace App\Http\Controllers\Masters;

use App\Models\AccountCategory;
use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Redirect;

class AccountCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all the nerds
        $accountcategory = new AccountCategory();
        $accountcategories = $accountcategory
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('accountcategory.index')
            ->with('accountcategories', $accountcategories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('accountcategory.create');
    }


    public function store(Request $request)
    {
        $rules = array(
//            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'name'       => 'required|alpha_dash|max:50',
            'description'      => 'required|alpha_dash|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('accountcategory/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $accountcategory = new AccountCategory();
            $accountcategory->name = Input::get('name');
            $accountcategory->description = Input::get('description');
            $accountcategory->active = 0;
            $accountcategory->approved = 0;
            $accountcategory->save();

            // redirect
            Session::flash('success', 'Account Category Created Successfully !');
            return Redirect::to('accountcategory');
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
        $accountcategory = AccountCategory::find($id);

        // show the view and pass the nerd to it
        return view('accountcategory.show')
            ->with('accountcategory', $accountcategory);
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
        Session::put('accountcategory', $id);
        $accountcategory = AccountCategory::find($id);

        // show the edit form and pass the nerd
        return view('accountcategory.edit')
            ->with('accountcategory', $accountcategory)
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
            'name'       => 'required|alpha_dash|max:50',
            'description' => 'required|alpha_dash',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('accountcategory/' . $id . '/edit/pendview')
                ->withErrors($validator);

        } else {
            // store
            $accountcategory = AccountCategory::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$accountcategory) {
                if($pendview) {
                    $accountcategory = AccountCategory::find($id);
                }else {
                    $accountcategory = new AccountCategory();
                    $accountcategory->oid = $id;
                }
            }
           // $accountcategory = new AccountCategory();
            $accountcategory->name = Input::get('name');
            $accountcategory->description = Input::get('description');
            $accountcategory->save();

            // redirect
            Session::flash('success', 'Account Category Created Successfully !');
            return Redirect::to('accountcategory');
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
        $table = new AccountCategory();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = AccountCategory::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Account Category deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('accountcategory');
    }
    public function getPendingAccCategory()
    {
        // Show All Pending Designation

        $accountcategory = new AccountCategory();
        $accountcategories = $accountcategory
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('accountcategory.pending')
            ->with('accountcategories', $accountcategories);
    }
    public function postPendingAccCategory($id)
    {
        // Show All Pending Designation
        AccountCategory::approvePending($id);
        return Redirect::to('accountcategory/pending');
    }
    public function reject($id)
    {
        $accountcategory = AccountCategory::find($id);
        $accountcategory->active = '1';
        $accountcategory->approved = '0';
        $accountcategory->save();
        return Redirect::to('accountcategory/pending');
    }
}
