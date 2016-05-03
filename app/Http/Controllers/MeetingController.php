<?php
namespace App;
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Company;
use App\Models\MeetingDoc;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PHPExcel_IOFactory;


class MeetingController extends Controller
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
        $company = Company::find($cmpid);
        $meeting = Meeting::where('companyid',$cmpid)->get();
        $meetingdoc = MeetingDoc::lists('name', 'id');
        return view('meeting.index')
            ->with('meeting', $meeting)
            ->with('company', $company)
            ->with('meetingdoc', $meetingdoc);
    }


    public function create()
    {
        //
        $meeting = Meeting::all();
        return view('meeting.create')
            ->with('meeting', $meeting);

    }

    public function store(Request $request)
    {
        //
        $cmpid = Session::get('cmpid');
        $name = $request->all('name');
//        $companyid = $request->old('companyid');

        $rules = array(
           // 'egdate' => 'required',
//            'file1' => 'required',
//            'file2' => 'required',
            'file1' => 'max:10000',
            'file2' => 'max:10000',
            'rnote' => 'required|string|max:25',
            'minute' => 'required|string|max:25',
            'decision' => 'required|string|max:25'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('meeting')
                ->withInput()
                ->withErrors($validator);
        } else {
            $file = $request->file('file1');
            if($file){
                $extension = $file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $filename = $file->getClientOriginalName();
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $entry = new MeetingDoc();
                    $entry->type = $file->getClientMimeType();
                    $entry->name = $file->getClientOriginalName();
                    $entry->extension = $file->getFilename() . '.' . $extension;
                    $entry->content = $file->getFilename() . '.' . $extension;
                    $entry->save();
                    $minupload = $entry->id;
                }
            }

            $file1 = $request->file('file2');
            if($file1){
                $extension = $file1->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $filename = $file1->getClientOriginalName();
                $upload_success = $file1->move($destinationPath, $filename);
                if ($upload_success) {
                    $entry = new MeetingDoc();
                    $entry->type = $file1->getClientMimeType();
                    $entry->name = $file1->getClientOriginalName();
                    $entry->extension = $file1->getFilename() . '.' . $extension;
                    $entry->content = $file1->getFilename() . '.' . $extension;
                    $entry->save();
                    $decisionlink = $entry->id;
                }
            }
            $meeting = new Meeting;
            $meetingtime = strtotime(Input::get('egdate'));
            $meetingdate = date('Y-m-d', $meetingtime);
            $meeting->egdate = $meetingdate;
            $meeting->companyid = $cmpid;
            $meeting->minupload = $file ? $minupload : '';
            $meeting->rnote = Input::get('rnote');
            $meeting->minute = Input::get('minute');
            $meeting->decision = Input::get('decision');
            $meeting->decisionlink = $file1 ? $decisionlink : '';
            $meeting->save();
            Session::flash('message', 'CDR Meeting created  Successfully !!');
            // return Redirect::back();
            return Redirect::to('meeting');
        }
    }
}
