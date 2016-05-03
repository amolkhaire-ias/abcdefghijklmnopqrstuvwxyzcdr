<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\BorrowerClass;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
class BorrowerClassController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // get all the nerds
        $borrowerclass = new BorrowerClass;
        $borrowerclasses = $borrowerclass
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('borrowerclass.index')
            ->with('borrowerclasses', $borrowerclasses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('borrowerclass.create');
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
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('borrowerclass/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $borrowerclass = new BorrowerClass;
            $borrowerclass->name = Input::get('name');
            $borrowerclass->description = Input::get('description');
            $borrowerclass->active = 0;
            $borrowerclass->approved = 0;
            $borrowerclass->save();

            // redirect
            Session::flash('success', 'Borrower class Created Successfully !');
            return Redirect::to('borrowerclass');
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
        $borrowerclass = BorrowerClass::find($id);

        // show the view and pass the nerd to it
        return view('borrowerclass.show')
            ->with('borrowerclass', $borrowerclass);
    }


    public function edit($id,$pendview)
    {
        // get the nerd
        $borrowerclass = BorrowerClass::find($id);

        // show the edit form and pass the nerd
        return view('borrowerclass.edit')
            ->with('borrowerclass', $borrowerclass)
            ->with('pendview', $pendview);
    }


    public function update($id)
    {
        $pendview = Input::get('pendview');
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('borrowerclass/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            $borrowerclass = BorrowerClass::where('oid','=',$id)
                ->where('name','=','name')
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$borrowerclass) {
                if($pendview) {
                    $borrowerclass = BorrowerClass::find($id);
                }else {
                    $borrowerclass = new BorrowerClass();
                    $borrowerclass->oid = $id;
                }
            }
            // store
//            $borrowerclass = BorrowerClass::find($id);
            $borrowerclass->name       = Input::get('name');
            $borrowerclass->description      = Input::get('description');
            $borrowerclass->save();

            // redirect
            Session::flash('message', 'Borrower Class updated Successfully !!');
            return Redirect::to('borrowerclass');
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
        $table = new borrowerclass();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = borrowerclass::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Borrower class deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('borrowerclass');
    }

    public function getPendingBorrowerClass()
    {
        // Show All Pending Borrower Class

        $borrowerclass = new BorrowerClass;
        $borrowerclasses = $borrowerclass
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('borrowerclass.pending')
            ->with('borrowerclasses', $borrowerclasses);
    }
    public function postPendingBorrowerClass($id)
    {
        BorrowerClass::approvePending($id);
        return Redirect::to('borrowerclass/pending');

    }
}
