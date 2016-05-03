<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\State;
use App\Models\Country;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class StateController extends Controller
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
        $countries = Country::where ('active','=',1)-> where ('approved','=',1)-> lists('countryname', 'id');
        $state = new State;
        $states = $state
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        // load the view and pass the nerds
        return view('state.index')
            ->with('states', $states)
            ->with('countries', $countries);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $countries = Country::where('active', '=', 1)
            ->where('approved', '=' ,1)
            ->lists('countryname', 'id');
        return view('state.create')
                ->with('countries',$countries);
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
            'countryid'       => 'required',
            'statecode'      => 'required|unique:states|max:100',
            'statename'      => 'required|max:200'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('state/create')
                ->withErrors($validator);
               
        } else {
            // store
            $state = new State;
            $state->countryid  = Input::get('countryid');
            $state->statecode = Input::get('statecode');
            $state->statename = Input::get('statename');
            $state->active = 0;
            $state->approved = 0;
            $state->save();
            // redirect
            Session::flash('success', 'State Created Successfully!');
            return Redirect::to('state');
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
        $state = State::find($id);
        // show the view and pass the nerd to it
        return view('state.show')
            ->with('state', $state);
    }
    
    
    public function getstatefromid($id)
    {
        // get the nerd
        $state = State::find($id);
        // show the view and pass the nerd to it
        return view('state.show')
            ->with('state', $state);
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
        $state = State::find($id);
        $countries = Country::lists('countryname', 'id');
        // show the edit form and pass the nerd
        return view('state.edit')
            ->with('state', $state)
            ->with('pendview', $pendview)
            ->with('countries',$countries);;
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
            'countryid'       => 'required',
            'statecode'      => 'required|max:100',
            'statename'      => 'required|max:200'
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('state/' . $id . '/edit')
                ->withErrors($validator);
               
        } else {
            // store
            $state = State::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$state) {
                if($pendview) {
                    $state = State::find($id);
                }else {
                    $state = new State();
                    $state->oid = $id;
                }
            }
          //  $state = State::find($id);
            $state->countryid  = Input::get('countryid');
            $state->statecode = Input::get('statecode');
            $state->statename = Input::get('statename');
            $state->save();

            // redirect
            Session::flash('success', 'State updated Successfully !!');
            return Redirect::to('state');
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
        $table = new State();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = State::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'State deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('state');
    }

    public function getPendingState()
    {
        // Show All Pending Designation

        $state = new State;
        $states = $state
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();
        return view('state.pending')
            ->with('states', $states);
    }
    public function postPendingState($id)
    {
        State::approvePending($id);
        return Redirect::to('state/pending');
    }
    public function reject($id)
    {
        $state = State::find($id);
        $state->active = '1';
        $state->approved = '0';
        $state->save();
        return Redirect::to('state/pending');
    }

}
