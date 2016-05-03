<?php
namespace App\Http\Controllers;
use App\Models\LenderDetail;
use App\Models\Pocontact;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\Lender;
use App\Models\Package;
use App\Models\Company;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;


class InstitutedetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($isedit = 0)
    {
        // load the view and pass the nerds
        $cmpid = Session::get('cmpid');
        $pkgid = Session::get('pkgid');
        $pocontacts = Pocontact::where('active','=',1)
            ->where('approved','=',1)
            ->where('companyid', '=', $cmpid)
            ->where('packageid', '=', $pkgid)
            ->get();

        $lenders = LenderDetail::where('active','=',1)
            ->where('approved','=',1)
            ->where('companyid', '=', $cmpid)
            ->get();
        $institutes = Institute::where('companyid','=',$cmpid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->first();
       // load the view and pass the nerds
        return view('institutedetail.index')
            ->with('institute', $institutes)
            ->with('lenders', $lenders)
            ->with('cmpid', $cmpid)
            ->with('pocontacts', $pocontacts)
            ->with('isedit', $isedit);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $packages = Package::where('active', '=', 1)->where('approved', '=', 1)->lists('packageid', 'id');
        $lenders = Lender::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->where('lendertype', '=', 1)
            ->lists('name', 'id');

        $companies = Company::where('active', '=', 1)->where('approved', '=', 1)->lists('name', 'id');
        return view('institutedetail.create')
            ->with('packages', $packages)
            ->with('lenders', $lenders)
            ->with('companies', $companies);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request )
    {
//        $status = $request->all();
        $cmpid = Session::get('cmpid');
        $pkgid = Session::get('pkgid');
        $pocid = Institute::where('companyid','=',$cmpid)->first();
//dd($pocid);
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'ritype' => 'required',
            'personname' => 'regex: /^[\pL\s]+$/u|max:50',
            'designation' => 'string|max:100',
            'email' => 'email|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('institutedetails')
                ->withInput()
                ->withErrors($validator);
        } else {
            if($pocid){
                    $person = Pocontact::find($pocid->contactid);
                    $person1 = Pocontact::find($pocid->contactid1);
                   $miinstitute = Institute::find($pocid->id);
            }else{
                $person = new Pocontact();
                $person1 = new Pocontact();
                $miinstitute = new Institute();
            }

        $person->name = Input::get('personname');
        $person->designation = Input::get('designation');
        $person->email =  Input::get('email');
        $person->contactno = Input::get('contactno');
        $person->companyid = $cmpid;
        $person->packageid = $pkgid;
        $person->active = 1;
        $person->approved = 1;
        $person->save();
        $personid = $person->id;
if($person1) {
    $person1->name = Input::get('personname1');
    $person1->designation = Input::get('designation1');
    $person1->email = Input::get('email1');
    $person1->contactno = Input::get('contactno1');
    $person1->companyid = $cmpid;
    $person1->packageid = $pkgid;
    $person1->active = 1;
    $person1->approved = 1;
    $person1->save();
    $personid1 = $person1->id;
}
        $miinstitute->leadbank = Input::get('leadbank');
        $miinstitute->packageid = $pkgid;
        $miinstitute->companyid = $cmpid;
        $miinstitute->mitype = Input::get('mitype');
        $miinstitute->ritype = Input::get('ritype');
        $miinstitute->branchname = Input::get('branchname');
        $miinstitute->branchadd = Input::get('branchadd');
        $miinstitute->trabank = Input::get('trabank');
            $time = strtotime(Input::get('tradate'));
        $date = date('Y-m-d',$time);
        $miinstitute->tradate = $date ? $date : '';
        $miinstitute->contactid = $personid;
            if ($person1){
        $miinstitute->contactid1 = $personid1;
            }
        $miinstitute->active = 1;
        $miinstitute->approved = 1;
        $miinstitute->save();

        Session::flash('success', 'Institute detail Created Successfully !');
        return Redirect::to('institutedetails');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $institute = Institute::find($id);

        // show the view and pass the nerd to it
        return view('institutedetail.show')
            ->with('institute', $institute);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $institute = Institute::find($id);
        $packages = Package::where('active', '=', 1)->where('approved', '=', 1)->lists('packageid', 'id');
        $lenders = Lender::where('active', '=', 1)->where('approved', '=', 1)->lists('name', 'id');
        $companies = Company::where('active', '=', 1)->where('approved', '=', 1)->lists('name', 'id');
        // show the edit form and pass the nerd
        return view('institutedetail.edit')
            ->with('institute', $institute)
            ->with('packages', $packages)
            ->with('lenders', $lenders)
            ->with('companies', $companies);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $institute = Institute::find($id);
        $institute->lenderid = Input::get('lenderid');
        $institute->packageid = Input::get('packageid');
        $institute->companyid = Input::get('companyid');
        $institute->insttype = Input::get('insttype');
        $institute->save();

        // redirect
        Session::flash('message', 'Institute detail updated Successfully !!');
        return Redirect::to('institutedetail');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $institute = Institute::find($id);
        $institute->active = 0;
        $institute->save();
        // redirect
        Session::flash('message', 'Institute deleted Successfully !!');
        return Redirect::to('institutedetail');
    }

    public function getPendingInstitute()
    {
        // Show All Pending Designation
        $institutes = new Institute();
        $institutes = $institutes
            ->where('active', '=', 0)
            ->where('approved', '=', 0)
            ->get();

        return view('institutedetail.pending')
            ->with('institutes', $institutes);
    }

    public function postPendingInstitute($id)
    {
        // Show All Pending Designation

        $institute = Institute::find($id);
        $institute->active = 1;
        $institute->approved = 1;
        $institute->save();
        // redirect
        return back();
    }

    public function getCdrData()
    {
        //$packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lendercdr = Lender::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->where('lendertype', '=', 1)
            ->get();
        return $lendercdr;
    }

    public function getNonCdrData()
    {
        //   $packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        $lendernoncdr = Lender::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->where('lendertype', '=', 2)
            ->get();
        return $lendernoncdr;


    }
}
