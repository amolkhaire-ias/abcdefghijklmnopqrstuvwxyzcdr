<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Country;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class CountryController extends Controller
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
        $country = new Country;
        $countries = $country
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('country.index')
            ->with('countries', $countries);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('country.create');
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
            'countrycode' => 'required|string|unique:countries|max:50',
            'countryname'      => 'required|string|max:50',
            'countrydescription'      => 'required|string|max:200',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('country/create')
                ->withErrors($validator);
               
        } else {
            // store
            $country = new Country;
            $country->countrycode = Input::get('countrycode');
            $country->countryname = Input::get('countryname');
            $country->countrydescription = Input::get('countrydescription');
            $country->active = 0;
            $country->approved = 0;
            $country->save();
            // redirect
            Session::flash('message', 'Country created Successfully !');
            return Redirect::to('country');
        }
       
    }


    public function show($id)
    {
        $country = Country::find($id);
        return view('country.show')
            ->with('country', $country);
    }


    public function edit($id,$pendview)
    {
        // get the nerd
        $country = Country::find($id);

        // show the edit form and pass the nerd
        return view('country.edit')
            ->with('country', $country)
            ->with('pendview', $pendview);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $pendview = Input::get('pendview');
        $rules = array(
            'countrycode' => 'required|string|max:50',
            'countryname'      => 'required|string|max:50',
            'countrydescription'      => 'required|string|max:200',
        );
        $validator = validator(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('country/' . $id . '/edit')
                ->withErrors($validator);
               
        } else {
            $country = Country::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$country) {
                if($pendview) {
                    $country = Country::find($id);
                }else {
                    $country = new Country();
                    $country->oid = $id;
                }
            }
            $country->countrycode = Input::get('countrycode');
            $country->countryname = Input::get('countryname');
            $country->countrydescription = Input::get('countrydescription');
            $country->save();
            // redirect
            Session::flash('message', 'Country updated Successfully !!');
            return Redirect::to('country');
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
        $table = new Country();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Country::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Country deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('country');
    }

    public function getPendingCountry()
    {
        // Show All Pending Designation

        $country = new Country;
        $countries = $country
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('country.pending')
            ->with('countries', $countries);
    }
    public function postPendingCountry($id)
    {
        Country::approvePending($id);
        return Redirect::to('country/pending');
    }
    public function reject($id)
    {
        $country = Country::find($id);
        $country->active = '1';
        $country->approved = '0';
        $country->save();
        return Redirect::to('country/pending');
    }

}
