<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class GroupController extends Controller
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
        //
        $group = new Group;
        $groups = $group
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('groups.index')
            ->with('groups', $groups);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('groups.create');
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
        $name = $request->all('name');
        $description = $request->old('description');


        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|regex: /^[\pL\s]+$/u|max:50',
            'description'      => 'required|string|max:255',

        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('groups/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $groups = new Group;
            $groups->name = Input::get('name');
            $groups->description = Input::get('description');
            $groups->active = 0;
            $groups->approved = 0;
            $groups->save();

            // redirect
            Session::flash('success', 'Groups Created Successfully !');
            return Redirect::to('groups');
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
        $group = Group::find($id);

        // show the view and pass the nerd to it
        return view('groups.show')
            ->with('groups', $group);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id , $pendview)
    {
        //
        $group =Group::find($id);

        // show the edit form and pass the nerd
        return view('groups.edit')
            ->with('group', $group)
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
            'description'      => 'required',

        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('groups/' . $id . '/edit')
                ->withErrors($validator);

        } else {

            $groups = Group::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$groups) {
                if($pendview) {
                    $groups = Group::find($id);
                }else {
                    $groups = new Group();
                    $groups->oid = $id;
                }
            }
            //$groups = Group::find($id);
            $groups->name       = Input::get('name');
            $groups->description      = Input::get('description');
            $groups->save();
            // redirect
            Session::flash('message', 'Promoters updated Successfully !!');
            return Redirect::to('groups');
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
        $table = new Group();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Group::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Groups deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('groups');
    }

    public function getPendingGroup()
    {
        // Show All Pending Designation

        $groups = new Group;
        $groups = $groups
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('groups.pending')
            ->with('groups', $groups);
    }
    public function postPendingGroup($id)
    {
        // Show All Pending Designation
        Group::approvePending($id);
        return Redirect::to('groups/pending');
    }
    public function reject($id)
    {
        $groups = Group::find($id);
        $groups->active = '1';
        $groups->approved = '0';
        $groups->save();
        return Redirect::to('groups/pending');
    }
}
