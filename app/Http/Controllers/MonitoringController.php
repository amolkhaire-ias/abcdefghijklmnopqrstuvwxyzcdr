<?php
namespace App;
namespace App\Http\Controllers;
use App\Models\BorrowerClass;
use Illuminate\Http\Request;
use App\Models\Monitoring;
use App\Models\Company;
use App\Models\MonitoringDoc;
//use App\Http\Controllers\Controller;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PHPExcel_IOFactory;


class MonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }
    public function index($isedit = 0)
    {
        $cmpid = Session::get('cmpid');
        $monitoring = Monitoring::where('companyid','=',$cmpid)
            ->get();
        $monitoringdoc = MonitoringDoc::lists('name', 'id');
        return view('monitoring.index')
        ->with('monitoring',$monitoring)
        ->with('monitoringdoc',$monitoringdoc)
        ->with('isedit',$isedit);
    }

    public function download($monitoringdoc){
        //PDF file is stored under project/public/download/info.pdf
        $file= public_path(). "/uploads/".$monitoringdoc;
        $headers = array(
            'Content-Type: application/pdf',
        );
        return Response::download($file, 'filename.pdf', $headers);
    }

    public function create()
    {
        $monitoring = Monitoring::all();
        $borrowid = BorrowerClass::where ('active','=',1)
            ->where('approved','=',1)
            ->lists('name', 'id');
        return view('monitoring.create')
            ->with('monitoring',$monitoring)
            ->with('borrowid',$borrowid);
    }


    public function store(Request $request)
    {
        $cmpid = Session::get('cmpid');
        $rules = array(
            'condition' => 'required|string|max:255',
            'compliancestatus' => 'string|max:255',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('monitoring')
                ->withInput()
                ->withErrors($validator);
        } else {
        $file = $request->file('monitoringdocs');
            if($file) {
                $extension = $file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $filename = $file->getClientOriginalName();
                $upload_success = $file->move($destinationPath, $filename);
                if($upload_success) {
                    $entry = new MonitoringDoc();
                    $entry->type = $file->getClientMimeType();
                    $entry->name = $file->getClientOriginalName();
                    $entry->extension = $file->getFilename() . '.' . $extension;
                    $entry->content = $file->getFilename() . '.' . $extension;
                    $entry->save();
                    $criticaldocstatusid=$entry->id;
                }
            }

        $monitoring = new Monitoring;
        $monitoring->companyid = $cmpid;
        $monitoring->monitoringdocsid = $file ? $criticaldocstatusid : 0;
        $monitoring->condition = Input::get('condition');
        $monitoring->compliancestatus = Input::get('compliancestatus');
        $monitoring->save();
        Session::flash('message', 'Monitor created Successfully !!');
        return Redirect::to('monitoring');

    }
    }
    public function updatemonitoring(){
        $cmpid = Session::get('cmpid');
        $monitoringids = Monitoring::where('companyid','=',$cmpid)
            ->get();
        foreach ($monitoringids as $monitoringid){
            $compliancestatus = Input::get('compliancestatus'.$monitoringid->id);
            $monitoring  = Monitoring::find($monitoringid->id);
            $monitoring->compliancestatus = $compliancestatus;
            $monitoring->save();
        }
        Session::flash('message', 'Compliance Status Updated Successfully!');
        return Redirect::to('monitoring');
    
}
}
