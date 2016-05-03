<?php

namespace App\Http\Controllers\CompanyLenderMap;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Lender;
use Session;
use App\Models\Company;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CompanyLenderMapController extends Controller
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

        $lenders = Lender::where('active', '=', 1)
        ->where('approved', '=', 1)
        ->where('lendertype', '=', 1)
        ->get();

        return view('companylendermap.index')
            ->with('lenders', $lenders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $implementations = Implementation::select('lenderid')
            ->where ('active','=',1)
            ->where ('approved','=',1)
            //->where('companyid', '=', $companyid);
            ->get();
        foreach ($implementations as $implementation) {
            //grid value stored to database
            $implementationid = Input::get('implementationid'.$implementation->lenderid);
            $implementation = Implementation::find($implementationid);
            $implementation->comborrow = Input::get('comborrow'.$implementation->lenderid,false);
            $implementationtime = strtotime(Input::get('comdate'.$implementation->lenderid));
            $implementationdate = date('Y-m-d',$implementationtime);
            $implementation->comdate = $implementationdate;
            $implementation->conreceived = Input::get('conreceived'.$implementation->lenderid,false);
            $implementation->save();
        }

        Session::flash('message', 'Designation Updated Successfully!');
        return Redirect::to('implementation');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function getCdrData()
    {
        //$packages = Package::where ('active','=',1)-> where ('approved','=',1)-> lists('packageid', 'id');
        //lendertype
        $lendertype = Input::get('lendertype');

        $lendercdr = Lender::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->where('lendertype', '=', $lendertype)
            ->get();
        return $lendercdr;
    }

}
