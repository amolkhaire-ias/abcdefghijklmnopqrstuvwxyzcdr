<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Package;
use App\Models\Company;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::where('active','=',1)-> where('approved','=',1)-> lists('name', 'id');
        $package = new Package;
        $packages = $package
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('package.index')
            ->with('packages', $packages)
            ->with('companies', $companies);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

       $companies = Company::where('active','=',1)
           ->where ('approved','=',1)
           ->orderBy('name','ASC')
           ->lists('name', 'id');
        return view('package.create')
            ->with('companies', $companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = $request->all('status');
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'packageid' => 'required|unique:packages|max:50',
            'description' => 'required|max:50',
            'packagedate' => 'required|date',
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('package/create')
                ->withInput()
                ->withErrors($validator);
        } else {

            $package = new Package();
// 	approved,active, 	packageid, companyid, insttype, 	lenderid
            $package->description = trim(Input::get('description'), '  ');
            $package->packageid = Input::get('packageid');
            $package->companyid = Input::get('companyid');
            $implementationtime = strtotime(Input::get('packagedate'));
            $implementationdate = date('Y-m-d',$implementationtime);
            $package->packagedate = $implementationdate;
           // $mcmember->packagedate = Input::get('packagedate');
            $package->active = 0;
            $package->approved = 0;
            $package->save();

            // redirect
            Session::flash('success', 'package Created Successfully !');
            return Redirect::to('package');
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
        $package = Package::find($id);

        // show the view and pass the nerd to it
        return view('package.show')
            ->with('package', $package);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        $package = Package::find($id);
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        // show the edit form and pass the nerd
        return view('package.edit')
            ->with('package', $package)
            ->with('pendview', $pendview)
            ->with('companies', $companies);

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
            'packageid' => 'required|max:50',
            'description' => 'required|max:50',
            'packagedate' => 'required|date',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('package/' . $id . '/edit')
                ->withErrors($validator);

        } else {
            $package = Package::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$package) {
                if($pendview) {
                    $package = Package::find($id);
                }else {
                    $package = new Package();
                    $package->oid = $id;
                }
            }
            // store

            $package->packageid       = Input::get('packageid');
            $package->description      = Input::get('description');
            $implementationtime = strtotime(Input::get('packagedate'));
            $implementationdate = date('Y-m-d',$implementationtime);
            $package->packagedate = $implementationdate;

            $package->save();

            // redirect
            Session::flash('message', 'Package updated Successfully !!');
            return Redirect::to('package');
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
        $table = new Package();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Package::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Successfully deleted the Package!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('package');
    }
    public function getPendingPackage()
    {
        // Show All Pending Designation
        $companies = Company::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');

        $packages = new Package;
        $packages = $packages
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('package.pending')
            ->with('companies', $companies)
            ->with('packages', $packages);
    }
    public function postPendingPackage($id)
    {
        Package::approvePending($id);
        return Redirect::to('package/pending');
    }
    public function reject($id)
    {
        $packages = Package::find($id);
        $packages->active = '1';
        $packages->approved = '0';
        $packages->save();
        return Redirect::to('package/pending');
    }
}
