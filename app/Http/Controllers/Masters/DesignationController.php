<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Designation;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class DesignationController extends Controller
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
        // get all the nerds
        $designation = new Designation;
        $designations = $designation
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('designation.index')
            ->with('designations', $designations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('designation.create');
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
            return Redirect::to('designation/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $designation = new Designation;
            $designation->name = Input::get('name');
            $designation->description = Input::get('description');
            $designation->active      = 0;
            $designation->approved      = 0;
            $designation->save();

            // redirect
            Session::flash('success', 'Designation Created Successfully !');
            return Redirect::to('designation');
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
        $designation = Designation::find($id);

        // show the view and pass the nerd to it
        return view('designation.show')
            ->with('designation', $designation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id,$pendview)
    {
        Session::put('currentid', $id);
        $designation = Designation::find($id);

        // show the edit form and pass the nerd
        return view('designation.edit')
            ->with('designation', $designation)
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
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('designation/' . $id . '/edit')
                ->withErrors($validator);
               
        } else {
            $designation = Designation::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$designation) {
                if($pendview) {
                    $designation = Designation::find($id);
                }else {
                    $designation = new Designation();
                    $designation->oid = $id;
                }
            }
            // store
           // $designation = Designation::find($id);
            $designation->name       = Input::get('name');
            $designation->description      = Input::get('description');
            $designation->save();

            // redirect
            Session::flash('message', 'Designation updated Successfully !!');
            return Redirect::to('designation');
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
        $table = new Designation();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Designation::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Designation deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('designation');
    }

    public function getPendingDesignation()
    {
        // Show All Pending Designation

        $designations = new Designation;
        $designations = $designations
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('designation.pending')
            ->with('designations', $designations);
    }
    public function postPendingDesignation($id)
    {
        // Show All Pending Designation

        Designation::approvePending( $id);
        return Redirect::to('designation/pending');

    }
    public function reject($id)
    {
        $designation = Designation::find($id);
        $designation->active = '1';
        $designation->approved = '0';
        $designation->save();
        return Redirect::to('designation/pending');
    }

}
