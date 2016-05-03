<?php

namespace App\Http\Controllers;

use App\Models\LenderDetail;
use App\Models\LenderType;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\FinParameter;
use App\Models\PostRestructuring;
use App\Models\Lender;
use App\Models\Company;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class PostRestructuringController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }


    public function index()
    {
        $cmpid = Session::get('cmpid');
        $lendercdr = LenderDetail::where('active','=',1)
            ->where('approved','=',1)
            ->where('lendertype',1)
            ->where('companyid',$cmpid)
            ->get();

        $lendertsm = LenderDetail::where('active','=',1)
            ->where('approved','=',1)
            ->where('lendertype',2)
            ->where('companyid',$cmpid)
            ->get();
//        $postrestructrings = PostRestructuring::where('companyid','=',$cmpid)->get();
        $lenders = Lender::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('name', 'id');
        $lendertypes = LenderType::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('name', 'id');

        $finparameters = FinParameter::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('financialratio', 'id');

        $parameterids = Postrestructuring::select('parameterid')
            ->groupby('parameterid')
            ->where('companyid','=',$cmpid)
            ->get();
        $lenderids = Postrestructuring::select('lenderid')
            ->groupby('lenderid')
            ->where('companyid','=',$cmpid)
            ->get();
        $postrestructrings = array();

        $lendertypesbyid = Postrestructuring::where('companyid','=',$cmpid)
            ->lists('lendertype','lenderid');
        foreach ($lenderids as $lenderid) {
            $postrestructring = '';
            foreach ($parameterids as $parameterid) {
                $postrestructring[] = Postrestructuring::select('lenderid', 'parameterid', 'amount', 'lendertype')
                    ->where('lenderid', '=', $lenderid['lenderid'])
                    ->where('parameterid', '=', $parameterid['parameterid'])
                    ->get();
            }
            $postrestructrings[] = $postrestructring;
        }
        return view('postrestructuring.index')
            ->with('lenders', $lenders)
            ->with('finparameters', $finparameters)
            ->with('postrestructrings', $postrestructrings)
            ->with('lendercdr', $lendercdr)
            ->with('parameterid', $parameterids)
            ->with('lendertsm', $lendertsm)
            ->with('lendertype', $lendertypes)
            ->with('lendertypesbyid', $lendertypesbyid)
            ->with('lenderids', $lenderids);
    }

    /**
     * Show the form for creating a new resource.
     *
//     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
//     * @param  \Illuminate\Http\Request $request
//     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request->all());
        $cmpid = Session::get('cmpid');
        $rules = array(
//            'lenderidcdr|lenderidtsp' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('postrestructuring')
                ->withInput()
                ->withErrors($validator);
        } else {
            $postrestructring = new PostRestructuring;
            $parameterids = explode(',', Input::get('parameterid'));
            foreach ($parameterids as $parameterid) {

                $lendertype = Input::get('type');
                if($lendertype == 1) {
                    $lenderid = Input::get('lenderidcdr');
                }elseif ($lendertype == 2) {
                    $lenderid = Input::get('lenderidtsp');
                }
                $postrestructring = $postrestructring->where('parameterid', '=', $parameterid)
                    ->where('lenderid', '=', $lenderid)
                    ->where('lendertype', '=', $lendertype)
                    ->first();
                if ($postrestructring == null) {
                    $postrestructring = new PostRestructuring;

                }
                $postrestructring->parameterid = Input::get('parameterid'.$parameterid);
                $postrestructring->lenderid = $lenderid;
                $postrestructring->companyid = $cmpid;
                $postrestructring->lendertype = $lendertype;
                $postrestructring->amount = Input::get('amount'.$parameterid);
                $postrestructring->active = 1;
                $postrestructring->approved = 1;
                $postrestructring->save();
            }
            Session::flash('success', 'postrestructuring created Successfully !');
            return Redirect::to('postrestructuring');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
//     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
//     * @param  \Illuminate\Http\Request $request
     * @param  int $id
//     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
//     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
