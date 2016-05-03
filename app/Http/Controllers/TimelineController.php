<?php

namespace App\Http\Controllers;

use App\Helpers\AppConstant;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\DocStageMap;
use App\Models\ExposureDetail;
use App\Models\Package;
use App\Models\Timeline;
use App\Models\TimelineDetail;
use App\Models\TimelineTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class TimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if (!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }

    public function index()
    {
        $cmpid = Session::get('cmpid');
        $pkgid = Session::get('pkgid');
        $cmptimeline = Timeline::getCmpTimeline($cmpid);

        $cmpname = Company::getCmpNameById($cmpid);
        $package = Package::getPkgByCmpid($cmpid);

        $exposer = ExposureDetail::getExposerTotal($cmpid);
        $sysrevs = TimelineTemplate::getUniqueSysRevByHexpo(0);
        $stageid = Timeline::getStageid($cmpid, $pkgid);
        $tlinehighexpo = TimelineTemplate::getStageHighexpo($stageid);

        $docstagemaplists = DocStageMap::getStageDoclist($cmpid, $pkgid);
        if ($exposer >= 500) {
            $highexpo = 0;
        } else {
            $highexpo = 1;
        }
        return view('timeline/index')
            ->with('cmpid', $cmpid)
            ->with('cmpname', $cmpname)
            ->with('package', $package)
            ->with('exposer', $exposer)
            ->with('highexpo', $highexpo)
            ->with('sysrevs', $sysrevs)
            ->with('cmptimeline', $cmptimeline)
            ->with('tlinehighexpo', $tlinehighexpo)
            ->with('docstagemaplists', $docstagemaplists);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
//    public function jlfhCreate() {
//        $alldata = Input::all();
//        $stagenum = $alldata['stagenum'];
//        $stageid = $alldata['stageid'];
//        $cmpid = Session::get('cmpid');
//        $pkgid = Session::get('pkgid');
//        if($stagenum == 2) {
//            $rules = array('actualdate-s2' => 'required|date');
//        }elseif($stagenum == 3) {
//            $rules['actualdate-s3'] = 'required|date';
//            if(isset($alldata['doclink-s3'])) {
//                $rules['docname-s3'] = 'required';
//            }
//        }elseif($stagenum == 4) {
//            $rules['actualdate-s4'] = 'required|date';
//            if(isset($alldata['doclink-s4'])) {
//                $rules['docname-s4'] = 'required';
//            }
//        }elseif($stagenum == 5) {
//            $rules['actualdate-s5'] = 'required|date';
//            if(isset($alldata['doclink-s5'])) {
//                $rules['doctype-s5'] = 'required';
//            }
//        }elseif($stagenum == 6) {
//            $rules['actualdate-s6'] = 'required|date';
//            if(isset($alldata['doclink-s6'])) {
//                $rules['docname-s6'] = 'required';
//                $rules['docdescription-s6'] = 'required';
//            }
//        }elseif($stagenum == 7) {
//            $rules['actualdate-s7'] = 'required|date';
//            if(isset($alldata['doclink-s7'])) {
//                $rules['docname-s7'] = 'required';
//                $rules['docdescription-s7'] = 'required';
//            }
//        }elseif($stagenum == 8) {
//            $rules['actualdate-s8'] = 'required|date';
//            if(isset($alldata['doclink-s8'])) {
//                $rules['docname-s8'] = 'required';
//            }
//        }elseif($stagenum == 9) {
//            $rules['actualdate-s9'] = 'required|date';
//            if(isset($alldata['doclink-s9'])) {
//                $rules['docname-s9'] = 'required';
//            }
//        }
//        $messages = array('actualdate-s2.required' => 'The SMA-2 Date is required.',
//            'actualdate-s2.date' => 'The SMA-2 Date is not a valid date.');
//
//        $validator = Validator::make(Input::all(), $rules, $messages);
//        if ($validator->fails()) {
//            return Redirect::to('timeline')
//                ->withInput()
//                ->withErrors($validator);
//        } else {
//            $actdate = $alldata['actualdate-s'.$stagenum];
//            Timeline::insertActualDate($cmpid, $stageid, $actdate);
//            $cmpdocid = CompanyDocument::createCmpDocs($cmpid, $pkgid, $alldata, $stagenum, 'above500');
//            if($cmpdocid){
//                DocStageMap::createDocStageMap($cmpid, $pkgid,$cmpdocid,$stageid);
//            }
//            Session::flash('success', 'Stage is Updated Successfully');
//            Session::flash('stagenum',$stagenum);
//            return redirect::to('timeline');
//        }
//    }
//    public function jlflCreate() {
//        $alldata = Input::all();
//        $stagenum = $alldata['stagenum'];
//        $stageid = $alldata['stageid'];
//        $cmpid = Session::get('cmpid');
//        $pkgid = Session::get('pkgid');
//        if($stagenum == 2) {
//            $rules = array('actualdate-s2' => 'required|date');
//        }elseif($stagenum == 3) {
//            $rules['actualdate-s3'] = 'required|date';
//            if(isset($alldata['doclink-s3'])) {
//                $rules['docname-s3'] = 'required';
//            }
//        }elseif($stagenum == 4) {
//            $rules['actualdate-s4'] = 'required|date';
//            if(isset($alldata['doclink-s4'])) {
//                $rules['docname-s4'] = 'required';
//            }
//        }elseif($stagenum == 5) {
//            $rules['actualdate-s5'] = 'required|date';
//            if(isset($alldata['doclink-s5'])) {
//                $rules['docname-s5'] = 'required';
//                $rules['docdescription-s5'] = 'required';
//            }
//        }elseif($stagenum == 6) {
//            $rules['actualdate-s6'] = 'required|date';
//            if(isset($alldata['doclink-s6'])) {
//                $rules['docname-s6'] = 'required';
//            }
//        }elseif($stagenum == 7) {
//            $rules['actualdate-s7'] = 'required|date';
//            if(isset($alldata['doclink-s7'])) {
//                $rules['docname-s7'] = 'required';
//            }
//        }
//        $messages = array('actualdate-s2.required' => 'The SMA-2 Date is required.',
//            'actualdate-s2.date' => 'The SMA-2 Date is not a valid date.');
//
//        $validator = Validator::make(Input::all(), $rules, $messages);
//        if ($validator->fails()) {
//            return Redirect::to('timeline')
//                ->withInput()
//                ->withErrors($validator);
//        } else {
//            $actdate = $alldata['actualdate-s'.$stagenum];
//            Timeline::insertActualDate($cmpid, $stageid, $actdate);
//            $cmpdocid = CompanyDocument::createCmpDocs($cmpid, $pkgid, $alldata, $stagenum,'below500');
//            if($cmpdocid){
//                DocStageMap::createDocStageMap($cmpid, $pkgid,$cmpdocid,$stageid);
//            }
//            Session::flash('success', 'Stage is Updated Successfully');
//            Session::flash('stagenum',$stagenum);
//            return redirect::to('timeline');
//        }
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
//            'cutoffdate' => 'required',
            'highexposure' => 'required',
            'timelinemasterid' => 'required',
            'timelinename' => 'required|string|max:255'
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            Session::flash('timelinetype', 2);
            return Redirect::to('timeline')
                ->withInput()
                ->withErrors($validator);
        } else {
            $cmpid = Session::get('cmpid');
            $packageid = Session::get('pkgid');
//            $cutoffdate = Input::get('cutoffdate');
            $highexposure = Input::get('highexposure');
            $timelinemasterid = Input::get('timelinemasterid');
            $timelinename = Input::get('timelinename');
            $timelinetemps = TimelineTemplate::getAllStages($highexposure, $timelinemasterid);
            Timeline::createCmpTimeline($cmpid, $packageid, $timelinetemps);
            TimelineDetail::createCmpTimelineDetail($cmpid, $packageid, $timelinemasterid, $timelinename,$highexposure);
            Session::flash('success', 'Timeline Created Sucessfully !!!');
            Session::flash('stagenum', 0);
            return redirect('timeline');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSysrevAjax()
    {
        $highexposer = Input::get('highexposer');
        $sysrevs = TimelineTemplate::getUniqueSysRevByHexpo($highexposer);
        return $sysrevs;
    }


    public function deleteDoc($cmpdocid)
    {
        $docstagemap = DocStageMap::where('docid', '=', $cmpdocid)
            ->first();
        $docstagemap->delete();
        $cmpdocument = CompanyDocument::find($cmpdocid);
        $cmpdocument->delete();
        Session::flash('error', 'Document is Removed Successfully');
        return redirect::to('timeline');
    }

    public function flash()
    {
        $alldata = Input::all();
        $stagenum = $alldata['stagenum'];
        $rules = array();
        if ($stagenum == 0) {

            if (isset($alldata['doclink-s0'])) {
                $rules['docname-s0'] = 'required';
            }else {
                $rules['actualdate-s0'] = 'required|date';
            }
        } elseif ($stagenum == 1) {
//            $rules['actualdate-s1'] = 'required|date';
            if (isset($alldata['doclink-s1'])) {
                $rules['doctype-s1'] = 'required';
            }
        } elseif ($stagenum == 2) {
//            $rules['actualdate-s2'] = 'required|date';
            if (isset($alldata['doclink-s2'])) {
                $rules['docname-s2'] = 'required';
            }
        } elseif ($stagenum == 3) {
//            $rules['actualdate-s3'] = 'required|date';
            if (isset($alldata['doclink-s3'])) {
                $rules['docname-s3'] = 'required';
            }
        }
        $messages = $this->timelineValidationMessages($stagenum);
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            Session::flash('timelinetype', 2);
            return Redirect::to('timeline')
                ->withInput()
                ->withErrors($validator);
        } else {
            $cmpid = Session::get('cmpid');
            $pkgid = Session::get('pkgid');
            if($stagenum == 0) {
                $sma2date = $alldata['actualdate-s0'];
                Timeline::updateFlashCmpTimeline($cmpid,$sma2date);
            }
            if($stagenum == 2) {
                $cdregapprovaldate = $alldata['actualdate-s2'];
                Timeline::updateFlashStage3CmpTimeline($cmpid,$cdregapprovaldate);
                $extension = $alldata['extension'];
                if($extension == 1) {
                    Timeline::updateExtensionDate($alldata,$cmpid);
                }
                    if($cdregapprovaldate){
                        $company  = Company::find($cmpid);
                        $oldstatusid = $company->statusid;
                        if($oldstatusid == 1){
                            $company->statusid = 3;
                            $company->save();
                        }
                    }
            }

            Timeline::updateFlashActualdate($alldata['id'], $alldata['actualdate-s' . $stagenum]);
            $cmpdocid = CompanyDocument::createTimelineDocs($cmpid, $pkgid, $alldata, $stagenum);
            if ($cmpdocid) {
                DocStageMap::createDocStageMap($cmpid, $pkgid, $cmpdocid, $alldata['id']);
            }
            Session::flash('success', 'Stage Updated Successfully !!');
            return redirect('timeline');
        }
    }

    public function jlfh()
    {
        $alldata = Input::all();
        $stagenum = $alldata['stagenum'];
        if ($stagenum == 0) {
            $rules = array('actualdate-s0' => 'required|date');
        } elseif ($stagenum == 1) {
            $rules = array('actualdate-s1' => 'required|date');
        } elseif ($stagenum == 2) {
            $rules['actualdate-s2'] = 'required|date';
            if (isset($alldata['doclink-s2'])) {
                $rules['docname-s2'] = 'required';
            }
        } elseif ($stagenum == 3) {
            $rules['actualdate-s3'] = 'required|date';
            if (isset($alldata['doclink-s3'])) {
                $rules['docname-s3'] = 'required';
            }
        } elseif ($stagenum == 4) {
            $rules['actualdate-s4'] = 'required|date';
            if (isset($alldata['doclink-s4'])) {
                $rules['doctype-s4'] = 'required';
            }
        } elseif ($stagenum == 5) {
            $rules['actualdate-s5'] = 'required|date';
            if (isset($alldata['doclink-s5'])) {
                $rules['docname-s5'] = 'required';
                $rules['docdescription-s5'] = 'required';
            }
        } elseif ($stagenum == 6) {
            $rules['actualdate-s6'] = 'date';
            if (isset($alldata['doclink-s6'])) {
                $rules['docname-s6'] = 'required';
                $rules['docdescription-s6'] = 'required';
            }
        } elseif ($stagenum == 7) {
            $rules['actualdate-s7'] = 'required|date';
            if (isset($alldata['doclink-s7'])) {
                $rules['docname-s7'] = 'required';
            }
        } elseif ($stagenum == 8) {
            $rules['actualdate-s8'] = 'required|date';
            if (isset($alldata['doclink-s8'])) {
                $rules['docname-s8'] = 'required';
            }
        }
        $messages = $this->timelineValidationMessages($stagenum);

        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            Session::flash('timelinetype', 0);
            return Redirect::to('timeline')
                ->withInput()
                ->withErrors($validator);
        } else {
            $cmpid = Session::get('cmpid');
            $pkgid = Session::get('pkgid');
            $alldata = Input::all();
            if($stagenum == 0) {
                $sma2date = $alldata['actualdate-s0'];
                Timeline::updateCmpTimeline($cmpid,$sma2date);
            }
            if($stagenum == 6) {
                $cdregdate = $alldata['actualdate-s6'];
                if($cdregdate){
                    $company  = Company::find($cmpid);
                    $oldstatusid = $company->statusid;
                    if($oldstatusid == 1){
                        $company->statusid = 3;
                        $company->save();
                    }
                }
            }
            Timeline::updateActualdate($alldata['id'], $alldata['actualdate-s' . $stagenum]);
            $cmpdocid = CompanyDocument::createTimelineDocs($cmpid, $pkgid, $alldata, $stagenum);
            if ($cmpdocid) {
                DocStageMap::createDocStageMap($cmpid, $pkgid, $cmpdocid, $alldata['id']);
            }
            Session::flash('success', 'Stage Updated Successfully !!');
            return redirect('timeline');
        }
    }

    public function jlfl()
    {
        $alldata = Input::all();
        $stagenum = $alldata['stagenum'];
        if ($stagenum == 0) {
            $rules = array('actualdate-s0' => 'required|date');
        } elseif ($stagenum == 1) {
            $rules = array('actualdate-s1' => 'required|date');
        } elseif ($stagenum == 2) {
            $rules['actualdate-s2'] = 'required|date';
            if (isset($alldata['doclink-s2'])) {
                $rules['docname-s2'] = 'required';
            }
        } elseif ($stagenum == 3) {
            $rules['actualdate-s3'] = 'required|date';
            if (isset($alldata['doclink-s3'])) {
                $rules['docname-s3'] = 'required';
            }
        } elseif ($stagenum == 4) {
            $rules['actualdate-s4'] = 'required|date';
            if (isset($alldata['doclink-s4'])) {
                $rules['docname-s4'] = 'required';
                $rules['docdescription-s4'] = 'required';
            }
        } elseif ($stagenum == 5) {
            $rules['actualdate-s5'] = 'required|date';
            if (isset($alldata['doclink-s5'])) {
                $rules['docname-s5'] = 'required';
            }
        } elseif ($stagenum == 6) {
            $rules['actualdate-s6'] = 'required|date';
            if (isset($alldata['doclink-s6'])) {
                $rules['docname-s6'] = 'required';
            }
        }
        $messages = $this->timelineValidationMessages($stagenum);
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            Session::flash('timelinetype', 1);
            return Redirect::to('timeline')
                ->withInput()
                ->withErrors($validator);
        } else {
            $cmpid = Session::get('cmpid');
            $pkgid = Session::get('pkgid');
            $alldata = Input::all();
            if($stagenum == 0) {
                $sma2date = $alldata['actualdate-s0'];
                Timeline::updateCmpTimeline($cmpid,$sma2date);
            }
            if($stagenum == 4) {
                $cdregdate = $alldata['actualdate-s4'];
                if($cdregdate){
                    $company  = Company::find($cmpid);
                    $oldstatusid = $company->statusid;
                    if($oldstatusid == 1){
                        $company->statusid = 3;
                        $company->save();
                    }
                }
            }
            Timeline::updateActualdate($alldata['id'], $alldata['actualdate-s' . $stagenum]);
            $cmpdocid = CompanyDocument::createTimelineDocs($cmpid, $pkgid, $alldata, $stagenum);
            if ($cmpdocid) {
                DocStageMap::createDocStageMap($cmpid, $pkgid, $cmpdocid, $alldata['id']);
            }
            Session::flash('success', 'Stage Updated Successfully !!');
            return redirect('timeline');
        }
    }

    public function docsByStageId()
    {
        $stageid = Input::get('stageid');
        $docstagemaps = DocStageMap::getDocidByStageid($stageid);
        foreach ($docstagemaps as $key => $docstagemap) {
            $cmpdocs[$key] = $docstagemap->companyDocument;
        }
        return json_encode($cmpdocs, $key);
    }

    public function timelineDetails()
    {
        $timelinedetails = TimelineDetail::all();
        return view('timeline/timelinedetail')
            ->with('timelinedetails', $timelinedetails);
    }

    public function deleteTimeline($tdetailid)
    {
        $timelinedetail = TimelineDetail::find($tdetailid);
        $companyid = $timelinedetail->companyid;
        $packageid = $timelinedetail->packageid;
        $highexposure = $timelinedetail->highexposure;
        $timelinemasterid = $timelinedetail->timelinemasterid;
        $timelinetemplates = TimelineTemplate::getStageTempIds($highexposure, $timelinemasterid);
        foreach ($timelinetemplates as $key => $timelinetemplate) {
            $stageid = $timelinetemplate->id;
            $timelines[$key] = Timeline::getCmptimelineIds($companyid, $packageid, $stageid);
        }
        foreach ($timelines as $key => $timeline) {
            $stageid = $timeline->id;
            $docstagemaps[$key] = DocStageMap::getDocStageMap($stageid);
        }

        foreach ($docstagemaps as $docstagemaparray) {
            foreach ($docstagemaparray as $docstagemap) {
                if (count($docstagemap) > 0) {
                    $docid = $docstagemap->docid;
                    $cmpdocument = CompanyDocument::find($docid);
                    if ($cmpdocument) {
                        $cmpdocument->delete();
                    }
                }
            }
        }
        foreach ($docstagemaps as $docstagemaparray) {
            foreach ($docstagemaparray as $docstagemap) {
                if (count($docstagemap) > 0) {
                    $docstagem = DocStageMap::find($docstagemap->id);
                    $docstagem->delete();

                }
            }
        }
        foreach ($timelines as $timeline) {
            $timeln = Timeline::find($timeline->id);
            $timeln->delete();

        }
        $timelinedetail = TimelineDetail::find($tdetailid);
        $timelinedetail->delete();

        Session::flash('error', 'Timeline Deleted Sucessfully !!!');
        return redirect('timeline/timelinedetails');
    }

    /**
     * @param $stagenum
     * @return array
     */
    public function timelineValidationMessages($stagenum)
    {
        $messages = array('actualdate-s' . $stagenum . '.required' => 'The actual date is required.',
            'actualdate-s' . $stagenum . '.date' => 'The actual date is not a valid date.',
            'docname-s' . $stagenum . '.required' => 'The document name is required.',
            'doctype-s' . $stagenum . '.required' => 'The document type is required',
            'docdescription-s' . $stagenum . '.required' => 'The document description is required');
        return $messages;
    }
}
