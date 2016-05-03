<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class CityController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // get all the nerds
        $city = new City;
        $countries = Country::lists('countryname', 'id');
        $states = State::lists('statename', 'id');
        $cities = $city
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        return view('city.index')
            ->with('cities', $cities)
            ->with('countries', $countries)
            ->with('states', $states);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $countries = Country::orderBy('countryname','ASC')
        ->lists('countryname', 'id');
        $states = State::orderBy('statename','ASC')
        ->lists('statename', 'id');
        return view('city.create')
            ->with('countries', $countries)
            ->with('states', $states);
    }

    public function test()
    {

        $countries = Country::lists('countrycode', 'shortname');
        $states = State::lists('statecode', 'statename');
        return view('city.test')
            ->with('countries', $countries)
            ->with('states', $states);

    }

    public function getStates($id)
    {
        $states = DB::table('states')->where('countrycode', $id)->get();
        return view('city.test')
            ->with('states', $states);
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
            'countryid' => 'required',
            'stateid' => 'required',
            'citycode' => 'required',
            'cityname' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('city/create')
                ->withErrors($validator);

        } else {
            // store
            $city = new City;
            $city->countryid = Input::get('countryid');
            $city->stateid = Input::get('stateid');
            $city->citycode = Input::get('citycode');
            $city->cityname = Input::get('cityname');
            $city->active = 0;
            $city->approved = 0;
            $city->save();
            // redirect
            Session::flash('success', 'City created Successfully !!');
            return Redirect::to('city');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // get the nerd
        $city = City::find($id);

        // show the view and pass the nerd to it
        return view('city.show')
            ->with('city', $city);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id,$pendview)
    {
        // get the nerd
        Session::put('citycode', $id);
        $city = City::find($id);
        $countries = Country::lists('countryname', 'id');
        $states = State::lists('statename', 'id');

        // show the edit form and pass the nerd
        return view('city.edit')
            ->with('countries', $countries)
            ->with('states', $states)
            ->with('pendview', $pendview)
            ->with('city', $city);
    }


    public function update($id)
    {

        $pendview = Input::get('pendview');
        $rules = array(
            // 'countrycode' => 'required',
            'cityname' => 'required',
            'citycode' => 'required',
        );
        $validator = validator(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('city/' . $id . '/edit')
                ->withErrors($validator);
        } else {
            $city = City::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$city) {
                if($pendview) {
                    $city = City::find($id);
                }else {
                    $city = new City();
                    $city->oid = $id;
                }
            }
            $city->countryid = Input::get('countryid');
            $city->stateid = Input::get('stateid');
            $city->citycode = Input::get('citycode');
            $city->cityname = Input::get('cityname');

            $city->save();
            Session::flash('success', 'City updated Successfully !!');
            return Redirect::to('city');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $table = new City();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = City::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'City deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('city');
    }

    public function getPendingCity()
    {
        // Show All Pending Designation

        $city = new City;
        $countries = Country::lists('countryname', 'id');
        $states = State::lists('statename', 'id');
        $cities = $city
            ->where('active', '=', 0)
            ->where('approved', '=', 0)
            ->get();
        return view('city.pending')
            ->with('cities', $cities)
            ->with('countries',$countries)
            ->with('states',$states);
    }

    public function postPendingCity($id)
    {
        City::approvePending($id);
        return Redirect::to('city/pending');
    }
    public function getStateDetail()
    {
        $countryId = Input::get('countryId');
        $state = new State();
        $states = $state->where('countryid', '=', $countryId)->get();
        return $states;
    }
    public function reject($id)
    {
        $city = City::find($id);
        $city->active = '1';
        $city->approved = '0';
        $city->save();
        return Redirect::to('city/pending');
    }
}