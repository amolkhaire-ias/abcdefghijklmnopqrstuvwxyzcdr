<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\FyYear;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class FyYearController extends Controller
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
        $fyyear = new Fyyear;
        $fyyears = $fyyear
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('fyyear.index')
            ->with('fyyears', $fyyears);
    }

  
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('fyyear.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required|unique:fyyears|string|max:50',
            'fromdate'       => 'required|date',
            'todate'       => 'required|date',
        );

        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('fyyear/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $fyyear = new Fyyear;
            
            $fyyear->name = Input::get('name');
            $time = strtotime(Input::get('fromdate'));
            $date = date('Y-m-d',$time);
            $fyyear->fromdate = $date;
            $time = strtotime(Input::get('todate'));
            $date = date('Y-m-d',$time);
            $fyyear->todate = $date;
            //$fyyear->todate = Input::get('todate');
            $fyyear->iscurrentyear = Input::get('iscurrentyear',false);
            $fyyear->active = 0;
            $fyyear->approved = 0;
            
            $fyyear->save();

            // redirect
            Session::flash('success', 'Financial Year Created Successfully !');
            return Redirect::to('fyyear');
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
        $fyyear = Fyyear::find($id);

        // show the view and pass the nerd to it
        return view('fyyear.show')
            ->with('fyyear', $fyyear);
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
        $fyyear = Fyyear::find($id);

        // show the edit form and pass the nerd
        return view('fyyear.edit')
            ->with('fyyear', $fyyear)
            ->with('pendview', $pendview);
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
//            'name'       => 'required|string|max:50',
            'name'       => 'unique:fyyears,name,'.$id

        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('fyyear/' . $id . '/edit/'.$pendview)
                ->withErrors($validator);
               
        } else {
            // store
            $fyyear = Fyyear::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$fyyear) {
                if($pendview) {
                    $fyyear = Fyyear::find($id);
                }else {
                    $fyyear = new Fyyear();
                    $fyyear->oid = $id;
                }
            }
            $fyyear->name = Input::get('name');
            $time = strtotime(Input::get('fromdate'));
            $date = date('Y-m-d',$time);
            $fyyear->fromdate = $date;
            $time = strtotime(Input::get('todate'));
            $date = date('Y-m-d',$time);
            $fyyear->todate = $date;
            $fyyear->iscurrentyear = Input::get('iscurrentyear',false);
//
//            if (Input::get('iscurrentyear') === 'yes') {
//            $fyyear->iscurrentyear = 1;
//            } else {
//             $fyyear->iscurrentyear = 0;
//            }
            $fyyear->save();
            // redirect
            Session::flash('message', ' Financial Year updated Successfully !!');
            return Redirect::to('fyyear');
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
        $table = new Fyyear();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Fyyear::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Financial Year deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('fyyear');
    }

    public function getPendingFyYears()
    {

        $fyyear = new Fyyear;
        $fyyears = $fyyear
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('fyyear.pending')
            ->with('fyyears', $fyyears);
    }
    public function postPendingFyYears($id)
    {
        Fyyear::approvePending($id);
        return Redirect::to('fyyear/pending');
    }
    public function reject($id)
    {
        $fyyear = Fyyear::find($id);
        $fyyear->active = '1';
        $fyyear->approved = '0';
        $fyyear->save();
        return Redirect::to('fyyear/pending');
    }

}
