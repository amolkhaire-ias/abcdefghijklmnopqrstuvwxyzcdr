<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Lenderaggrstatus;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class LenderaggrstatusController extends Controller
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
        $lenderaggrstatus = new Lenderaggrstatus;
        $lenderaggrstatdetails = $lenderaggrstatus
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();
        // load the view and pass the nerds
        return view('lenderaggrstatus.index')
            ->with('lenderaggrstatdetails', $lenderaggrstatdetails);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('lenderaggrstatus.create');
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
            'status'       => 'required|string|unique:lenderaggrstatus|max:50',
            'description'      => 'required|string|max:255'
        );
        $validator = Validator::make(Input::all(), $rules);
        // process the login
        if ($validator->fails()) {
            return Redirect::to('lenderaggrstatus/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            // store
            $lenderaggrstatus = new Lenderaggrstatus;
            $lenderaggrstatus->status = Input::get('status');
            $lenderaggrstatus->description = Input::get('description');
            $lenderaggrstatus->active = 0;
            $lenderaggrstatus->approved = 0;
            $lenderaggrstatus->save();
            // redirect
            Session::flash('success', 'Lender Aggreable status Created Successfully !');
            return Redirect::to('lenderaggrstatus');
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
        $lenderaggrstatus = Lenderaggrstatus::find($id);

        // show the view and pass the nerd to it
        return view('lenderaggrstatus.show')
            ->with('lenderaggrstatus', $lenderaggrstatus);
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
        $lenderaggrstatus = Lenderaggrstatus::find($id);

        // show the edit form and pass the nerd
        return view('lenderaggrstatus.edit')
            ->with('lenderaggrstatus', $lenderaggrstatus)
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
            'status'       => 'required',
            'description'      => 'required',
        );
        $validator = validator(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('lenderaggrstatus')
                ->withErrors($validator);
        } else {
            $lenderaggrstatus = Lenderaggrstatus::where('oid','=',$id)
                ->where('active','=',0)
                ->where('approved','=',0)
                ->first();
            if(!$lenderaggrstatus) {
                if($pendview) {
                    $lenderaggrstatus = Lenderaggrstatus::find($id);
                }else {
                    $lenderaggrstatus = new Lenderaggrstatus();
                    $lenderaggrstatus->oid = $id;
                }
            }
            // store
           // $lenderaggrstatus = Lenderaggrstatus::find($id);
            $lenderaggrstatus->status       = Input::get('status');
            $lenderaggrstatus->description      = Input::get('description');
            $lenderaggrstatus->save();
            // redirect
            Session::flash('message', 'Lender Aggreable Status updated Successfully !!');
            return Redirect::to('lenderaggrstatus');
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
        $table = new Lenderaggrstatus();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = Lenderaggrstatus::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Lender Agrreable Status deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('lenderaggrstatus');
    }

    public function getPendingLenderAgrrStatus()
    {

        $lenderaggrstatus = new Lenderaggrstatus;
        $lenderaggrstatuses = $lenderaggrstatus
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('lenderaggrstatus.pending')
            ->with('lenderaggrstatuses', $lenderaggrstatuses);
    }
    public function postPendingLenderAgrrStatus($id)
    {
        Lenderaggrstatus::approvePending($id);
        return Redirect::to('lenderaggrstatus/pending');
    }
    public function reject($id)
    {
        $lenderaggrstatus = Lenderaggrstatus::find($id);
        $lenderaggrstatus->active = '1';
        $lenderaggrstatus->approved = '0';
        $lenderaggrstatus->save();
        return Redirect::to('lenderaggrstatus/pending');
    }

}
