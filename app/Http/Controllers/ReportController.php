<?php
namespace App;
namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Institute;
use App\Models\Lender;
use App\Models\ExposureDetail;
use App\Models\Timeline;
use App\Models\TimelineDetail;
use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Carbon;
class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        return view('report.index');

    }

    public function report_1()
    {
        $reworkcases = Company::getReworkReport();
//        $reworkcase = Company::getCompanyReport();
//        $reworkcases = Institute::getCompanyReport();
//        $lender = Lender::lists('name','id');
        return view('report.report1')
//            ->with('lender',$lender)
            ->with('reworkcases',$reworkcases);
    }

    public function report2Index() {

        return view('report.report2Index');
    }

    public function report_2()
    {
        $selectedfromyear = Input::get('fromyear');
        $selectedtoyear = Input::get('toyear');
        $fromyear = $selectedfromyear.'-04-01';
        $toyear = $selectedtoyear.'-03-31';
        $referreddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->whereIn('statusid', [1,5,3,4,6,7])
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $referredexposure = array();
        foreach ($referreddata as $key => $refered) {
            $referredexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($refered->id);
            $referredexposure[$key]['id'] = $refered->id;
            $referredexposure[$key]['month'] = $refered->month;
        }

        $livedata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 1)
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $liveexposure = array();
        foreach ($livedata as $key => $lived) {
            $liveexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($lived->id);
            $liveexposure[$key]['id'] = $lived->id;
            $liveexposure[$key]['month'] = $lived->month;
        }

        $approveddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->whereIn('statusid', [3,4,6,7])
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $approvedexposure = array();
        foreach ($approveddata as $key => $approved) {
            $approvedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($approved->id);
            $approvedexposure[$key]['id'] = $approved->id;
            $approvedexposure[$key]['month'] = $approved->month;
        }

        $underimplementationdata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 3)
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $underimplementationexposure = array();
        foreach ($underimplementationdata as $key => $underimplementation) {
            $underimplementationexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($underimplementation->id);
            $underimplementationexposure[$key]['id'] = $underimplementation->id;
            $underimplementationexposure[$key]['month'] = $underimplementation->month;
        }

        $fullyimplementeddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 4)
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $fullyimplementedexposure = array();
        foreach ($fullyimplementeddata as $key => $fullyimplemented) {
            $fullyimplementedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($fullyimplemented->id);
            $fullyimplementedexposure[$key]['id'] = $fullyimplemented->id;
            $fullyimplementedexposure[$key]['month'] = $fullyimplemented->month;
        }
        $rejecteddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 5)
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $rejectedexposure = array();
        foreach ($rejecteddata as $key => $rejected) {
            $rejectedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($rejected->id);
            $rejectedexposure[$key]['id'] = $rejected->id;
            $rejectedexposure[$key]['month'] = $rejected->month;
        }


        $failuredata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 6)
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $failureexposure = array();
        foreach ($failuredata as $key => $failure) {
            $failureexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($failure->id);
            $failureexposure[$key]['id'] = $failure->id;
            $failureexposure[$key]['month'] = $failure->month;
        }

        $exiteddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 7)
            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $exitedexposure = array();
        foreach ($exiteddata as $key => $exited) {
            $exitedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($exited->id);
            $exitedexposure[$key]['id'] = $exited->id;
            $exitedexposure[$key]['month'] = $exited->month;
        }
//        dd($exitedexposure);
        return view('report.report2')
            ->with('referredexposure', $referredexposure)
            ->with('liveexposure', $liveexposure)
            ->with('approvedexposure', $approvedexposure)
            ->with('underimplementationexposure', $underimplementationexposure)
            ->with('fullyimplementedexposure', $fullyimplementedexposure)
            ->with('rejectedexposure', $rejectedexposure)
            ->with('failureexposure', $failureexposure)
            ->with('exitedexposure', $exitedexposure)
            ->with('fromyear', $selectedfromyear)
            ->with('toyear', $selectedtoyear);

    }

    public function report_3()
    {
        $exposure = array();
        $livecases = Company::getLiveCasesReport3();
        foreach ($livecases as $key => $case){
            $temp = TimelineDetail::getreporthighexpoByCmpid($case->cid);
            $exposure[$key]['actualdate'] = '';
            if($temp){
                if($temp->highexposure == 0){
                    $ctimeline = Timeline::getCmpTimeline($case->cid);
                    $actualdate = $ctimeline[6]->actualdate;
                }elseif ($temp->highexposure == 1){
                    $ctimeline = Timeline::getCmpTimeline($case->cid);
                    $actualdate = $ctimeline[4]->actualdate;
                }elseif ($temp->highexposure == 2){
                    $ctimeline = Timeline::getCmpTimeline($case->cid);
                    $actualdate = $ctimeline[2]->actualdate;
                }
                $exposure[$key]['actualdate'] = $actualdate;
            }
            $exposure[$key]['cid'] = $case->cid;
            $exposure[$key]['cname'] = $case->cname;
            $exposure[$key]['iname'] = $case->iname;
            $exposure[$key]['mitype'] = $case->mitype;
            $exposure[$key]['ritype'] = $case->ritype;
            $exposure[$key]['lname'] = $case->lname;
            $exposure[$key]['exposure'] = ExposureDetail::getFirstExposerTotal($case->cid);
            $exposure[$key]['lexposure'] = ExposureDetail::getLenderExposerTotal($case->cid);
        }
        $lenders = Lender::select('name','id')->get();
        return view('report.report3')
            ->with('livecases',$livecases)
            ->with('exposure',$exposure)
            ->with('lenders',$lenders);
    }


    public function report_4()
    {
        $exposure = array();
        $livecases = Company::getLiveCasesReport3();
        foreach ($livecases as $key => $case){
            $temp = TimelineDetail::getreporthighexpoByCmpid($case->cid);
            $exposure[$key]['actualdate'] = '';
            if($temp){
                if($temp->highexposure == 0){
                    $ctimeline = Timeline::getCmpTimeline($case->cid);
                    $actualdate = $ctimeline[6]->actualdate;
                }elseif ($temp->highexposure == 1){
                    $ctimeline = Timeline::getCmpTimeline($case->cid);
                    $actualdate = $ctimeline[4]->actualdate;
                }elseif ($temp->highexposure == 2){
                    $ctimeline = Timeline::getCmpTimeline($case->cid);
                    $actualdate = $ctimeline[2]->actualdate;
                }
                $exposure[$key]['actualdate'] = $actualdate;
            }
            $exposure[$key]['cid'] = $case->cid;
            $exposure[$key]['cname'] = $case->cname;
            $exposure[$key]['iname'] = $case->iname;
            $exposure[$key]['mitype'] = $case->mitype;
            $exposure[$key]['ritype'] = $case->ritype;
            $exposure[$key]['lname'] = $case->lname;
            $exposure[$key]['exposure'] = ExposureDetail::getFirstExposerTotal($case->cid);
            $exposure[$key]['exposurenoncdr'] = ExposureDetail::getNonCdrExposerTotal($case->cid);
            $exposure[$key]['lexposure'] = ExposureDetail::getLenderExposerTotal($case->cid);
        }
        $lenders = Lender::select('name','id')->get();
        return view('report.report4')
            ->with('livecases',$livecases)
            ->with('exposure',$exposure)
            ->with('lenders',$lenders);
    }


    public function report_5()
    {
        return view('report.report5');
    }

    public function report_6()
    {
        $exposure = array();
        $livecase = Company::getLiveCasesReport();
        foreach ($livecase as $key => $case){
            $exposure[$key]['cid'] = $case->cid;
            $exposure[$key]['cname'] = $case->cname;
            $exposure[$key]['iname'] = $case->iname;
            $exposure[$key]['mitype'] = $case->mitype;
            $exposure[$key]['ritype'] = $case->ritype;
            $exposure[$key]['lname'] = $case->lname;
            $exposure[$key]['exposure'] = ExposureDetail::getFirstExposerTotal($case->cid);
        }
        $lenders = Lender::select('name','id')->get();
        return view('report.report6')
            ->with('livecases',$livecase)
            ->with('lenders',$lenders)
            ->with('exposure',$exposure);
    }

    public function report_7()
    {
        return view('report.report7');
    }

    public function report_8a()
    {
//        $selectedfromyear = Input::get('fromyear');
//        $selectedtoyear = Input::get('toyear');
//        $fromyear = $selectedfromyear.'-04-01';
//        $toyear = $selectedtoyear.'-03-31';
        $referreddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->whereIn('statusid', [1,5,3,4,6,7])
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $referredexposure = array();
        foreach ($referreddata as $key => $refered) {
            $referredexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($refered->id);
            $referredexposure[$key]['id'] = $refered->id;
            $referredexposure[$key]['month'] = $refered->month;
        }

        $livedata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 1)
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $liveexposure = array();
        foreach ($livedata as $key => $lived) {
            $liveexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($lived->id);
            $liveexposure[$key]['id'] = $lived->id;
            $liveexposure[$key]['month'] = $lived->month;
        }

        $approveddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->whereIn('statusid', [3,4,6,7])
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $approvedexposure = array();
        foreach ($approveddata as $key => $approved) {
            $approvedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($approved->id);
            $approvedexposure[$key]['id'] = $approved->id;
            $approvedexposure[$key]['month'] = $approved->month;
        }

        $underimplementationdata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 3)
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $underimplementationexposure = array();
        foreach ($underimplementationdata as $key => $underimplementation) {
            $underimplementationexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($underimplementation->id);
            $underimplementationexposure[$key]['id'] = $underimplementation->id;
            $underimplementationexposure[$key]['month'] = $underimplementation->month;
        }

        $fullyimplementeddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 4)
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $fullyimplementedexposure = array();
        foreach ($fullyimplementeddata as $key => $fullyimplemented) {
            $fullyimplementedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($fullyimplemented->id);
            $fullyimplementedexposure[$key]['id'] = $fullyimplemented->id;
            $fullyimplementedexposure[$key]['month'] = $fullyimplemented->month;
        }

        $rejecteddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 5)
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $rejectedexposure = array();
        foreach ($rejecteddata as $key => $rejected) {
            $rejectedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($rejected->id);
            $rejectedexposure[$key]['id'] = $rejected->id;
            $rejectedexposure[$key]['month'] = $rejected->month;
        }


        $failuredata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 6)
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $failureexposure = array();
        foreach ($failuredata as $key => $failure) {
            $failureexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($failure->id);
            $failureexposure[$key]['id'] = $failure->id;
            $failureexposure[$key]['month'] = $failure->month;
        }

        $exiteddata = Company::select([DB::raw('DAY(referreddate) as day'),
            DB::raw('MONTH(referreddate) as month'),DB::raw('YEAR(referreddate) as year')
            ,'statusid','id'])
            ->where('statusid', '=', 7)
//            ->whereBetween(DB::raw("referreddate"),[$fromyear,$toyear])
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        $exitedexposure = array();
        foreach ($exiteddata as $key => $exited) {
            $exitedexposure[$key]['exposure'] = ExposureDetail::getExposerTotal($exited->id);
            $exitedexposure[$key]['id'] = $exited->id;
            $exitedexposure[$key]['month'] = $exited->month;
        }
        return view('report.report8a')
            ->with('referredexposure', $referredexposure)
            ->with('liveexposure', $liveexposure)
            ->with('approvedexposure', $approvedexposure)
            ->with('underimplementationexposure', $underimplementationexposure)
            ->with('fullyimplementedexposure', $fullyimplementedexposure)
            ->with('rejectedexposure', $rejectedexposure)
            ->with('failureexposure', $failureexposure)
            ->with('exitedexposure', $exitedexposure);
    }

    public function report_8b()
    {
        $exptotal = array();
        $counttotal = array();
        $livecase = array();
        $industries = Industry::select('id','name')->orderBy('name')->where('active',1)->where('approved',1)->get();
        foreach($industries as $key => $industry){
            $livecase[$key]['id'] = $industry->id;
            $livecase[$key]['name'] = $industry->name;
            $livecase[$key]['exposure'] = Company::getLiveCasesReport8b($industry->id);
            $livecase[$key]['count'] = Company::getCountCasesReport8b($industry->id);
            $exptotal[] = $livecase[$key]['exposure'];
            $counttotal[] = $livecase[$key]['count'];
        }
        $count = array_sum($counttotal);
        $grandtotal = array_sum($exptotal);
        return view('report.report8b')
            ->with('total',$grandtotal)
            ->with('count',$count)
            ->with('industries',$livecase);
    }

}