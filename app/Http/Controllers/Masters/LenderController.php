<?php

namespace App\Http\Controllers\Masters;
use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use App\Models\Lender;
use App\Models\LenderType;
use App\Models\Company;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class LenderController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // load the view and pass the nerds

        $lender = new Lender;
        $lenders = $lender
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('lenders.index')
            ->with('lenders', $lenders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

       return view('lenders.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|Regex:/^[A-Za-z0-9\-!,\"\/@\.:\(\)]+&/|max:50',
            'description' => 'required|string|max:255'
        );

        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('lenders/create')
                ->withInput()
                ->withErrors($validator);
        } else {

            $lender = new Lender();
// 	approved,active, 	packageid, companyid, insttype, 	lenderid
            $lender->name = Input::get('name');
            $lender->description = Input::get('description');
            $lender->active = 0;
            $lender->approved = 0;
            $lender->save();

            // redirect
            Session::flash('success', 'Lender Created Successfully !');
            return Redirect::to('lenders');
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
        $lenders = Lender::find($id);

        // show the view and pass the nerd to it
        return view('lenders.show')
            ->with('lenders', $lenders);
    }

    public function edit($id,$pendview)
    {
        $lenders = Lender::find($id);
        $lendertypes = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        // show the edit form and pass the nerd
        return view('lenders.edit')
            ->with('lenders', $lenders)
            ->with('pendview', $pendview)
            ->with('lendertypes', $lendertypes);
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
            'name'       => 'required|Regex:/^[A-Za-z0-9\-! ,\'\"\/@\.:\(\)]+&|max:50',
            'description' => 'required|string|max:50',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('lenders/create')
                ->withInput()
                ->withErrors($validator);
        } else {

            $lender = Lender::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$lender) {
                if($pendview) {
                    $lender = Lender::find($id);
                }else {
                    $lender = new Lender();
                    $lender->oid = $id;
                }
            }

            $lender->name = Input::get('name');
            $lender->description = Input::get('description');
            $lender->save();

            // redirect
            Session::flash('message', 'Lender updated Successfully !!');
            return Redirect::to('lenders');
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
        $table = new Lender();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Lender::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Lender deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('lenders');
    }
    public function getPendingLender()
    {
        // Show All Pending Designation
        $lendertypes = LenderType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $lender = new Lender();
        $lender = $lender
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('lenders.pending')
            ->with('lenders', $lender)
            ->with('lendertypes', $lendertypes);
    }
    public function postPendingLender($id)
    {
        Lender::approvePending($id);
        return Redirect::to('lenders/pending');
    }
    public function reject($id)
    {
        $lender = Lender::find($id);
        $lender->active = '1';
        $lender->approved = '0';
        $lender->save();
        return Redirect::to('lenders/pending');
    }
}
