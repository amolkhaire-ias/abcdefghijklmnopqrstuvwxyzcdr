<?php

namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Session;
use App\Models\PerformanceParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
class PerformanceParameterController extends Controller
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


        $performancepara = new PerformanceParameter;
        $performanceparas = $performancepara
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('performanceparameter.index')
            ->with('performanceparas', $performanceparas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('performanceparameter.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $parametername = $request->all('parametername');
        $description = $request->old('parameterdesc');
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'parametername'       =>  'required|regex: /^[\pL\s]+$/u|max:50',
            'parameterdesc'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('performanceparameter/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $performancepara = new PerformanceParameter;
            $performancepara->parametername = Input::get('parametername');
            $performancepara->parameterdesc = Input::get('parameterdesc');
            $performancepara->active = 0;
            $performancepara->approved = 0;
            $performancepara->save();

            // redirect
            Session::flash('success', 'Performance Parameter Created Successfully !');
            return Redirect::to('performanceparameter');
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
        $performancepara = PerformanceParameter::find($id);

        // show the view and pass the nerd to it
        return view('performanceparameter.show')
            ->with('performancepara', $performancepara);
    }

    /**
     * Show the form for editing the specified resource.
     *;
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$pendview)
    {
        // get the nerd
        $performancepara = PerformanceParameter::find($id);

        // show the edit form and pass the nerd
        return view('performanceparameter.edit')
            ->with('performancepara', $performancepara)
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
            'parametername'       =>  'required|regex: /^[\pL\s]+$/u|max:50',
            'parameterdesc'      =>  'required|string',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('performanceparameter')
                ->withErrors($validator);

        } else {
            $performancepara = PerformanceParameter::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$performancepara) {
                if($pendview) {
                    $facilities = PerformanceParameter::find($id);
                }else {
                    $performancepara = new PerformanceParameter();
                    $performancepara->oid = $id;
                }
            }
            // store
            //$performancepara = PerformanceParameter::find($id);
            $performancepara->parametername       = Input::get('parametername');
            $performancepara->parameterdesc      = Input::get('parameterdesc');
            $performancepara->save();

            // redirect
            Session::flash('message', 'Performance Parameter updated Successfully !!');
            return Redirect::to('performanceparameter');
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
        $table = new PerformanceParameter();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = PerformanceParameter::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Performance Parameter deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('performanceparameter');
    }
    public function getPendingPerformanceParameter()
    {
        // Show All Pending Designation

        $performanceparas = new PerformanceParameter;
        $performanceparas = $performanceparas
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('performanceparameter.pending')
            ->with('performanceparas', $performanceparas);
    }
    public function postPendingperformanceParameter($id)
    {
        PerformanceParameter::approvePending($id);
        return Redirect::to('performanceparameter/pending');
    }
    public function reject($id)
    {
        $performanceparas = PerformanceParameter::find($id);
        $performanceparas->active = '1';
        $performanceparas->approved = '0';
        $performanceparas->save();
        return Redirect::to('performanceparameter/pending');
    }
}
