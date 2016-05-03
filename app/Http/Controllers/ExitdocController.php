<?php
namespace App;
namespace App\Http\Controllers;
use App\Models\BorrowerClass;
use App\Models\Company;
use App\Models\Exitdoc;
use App\Models\ExitReason;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;

use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PHPExcel_IOFactory;


class ExitdocController extends Controller
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

        $exit = Exitdoc::all();
        $exitreasons = ExitReason::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('exitreason', 'id');
        return view('exit.index')
        ->with('exit',$exit)
            ->with('exitreasons',$exitreasons);
    }

    public function create()
    {
        //
        $exit = Exitdoc::all();
        return view('exit.create')
            ->with('exit',$exit);
    }


    public function store(Request $request)
    {
        $cmpid = Session::get('cmpid');
        $rules = array(
            'file1' => 'required',
            'file2' => 'required',
            'rnote' => 'required|string|max:25',
            'decision' => 'required|string|max:25',
            'date' => 'required|date',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('exit')
                ->withInput()
                ->withErrors($validator);
        } else {
        $file = $request->file('file1');
        $extension = $file->getClientOriginalExtension();
        $destinationPath = 'uploads/exitreason';
        $filename = $file->getClientOriginalName();
        $upload_success = $file->move($destinationPath, $filename);
        if($upload_success) {
            $rnotedoc = $file->getClientOriginalName();
        }
        $file = $request->file('file2');
        $extension = $file->getClientOriginalExtension();
        $destinationPath = 'uploads/exitreason';
        $filename = $file->getClientOriginalName();
        $upload_success = $file->move($destinationPath, $filename);
        if($upload_success) {
            $decisiondoc = $file->getClientOriginalName();
        }
        $exitdoc = new Exitdoc;
        $exitdoc->rnote = Input::get('rnote');
        $exitdoc->companyid = $cmpid;
        $exitdoc->rnotedoc = $rnotedoc;
        $exitdoc->decision = Input::get('decision');
        $exitdoc->decisiondoc = $decisiondoc;
        $exitdoc->reasonid = Input::get('reasonid');
        $reasonid = Input::get('reasonid');
        $time = strtotime(Input::get('date'));
        $date = date('Y-m-d', $time);
        $exitdoc->date = $date;
        $exitdoc->remark = Input::get('remark');
        $exitdoc->save();
        $statusid = $this->getStatusidByReason($reasonid);
        Company::updateCmpStatus($cmpid,$statusid);
        Session::flash('message', 'Exit Document created Successfully !!');
        return Redirect::to('exit');

        }
    }

    /**
     * @param $reasonid
     * @return int
     */
    public function getStatusidByReason($reasonid)
    {

        if ($reasonid == 1 || $reasonid == 2) {
            $statusid = 5;
            return $statusid;
        } elseif ($reasonid == 3) {
            $statusid = 6;
            return $statusid;
        } elseif ($reasonid == 4 || $reasonid == 5) {
            $statusid = 7;
            return $statusid;
        }
    }
}
