<?php

namespace App\Models;

use App\Helpers\HelperServiceProvider;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    protected $table = 'companytimelines';
    public function timelinetemp()
    {
        return $this->belongsTo('App\Models\TimelineTemplate', 'stageid');
    }

    public static function createCmpTimeline($cmpid, $packageid, $timelinetemps)
    {
        foreach ($timelinetemps as $timelinetemp) {
            $expirydays = $timelinetemp->expirydays;
            $timeline = new Timeline();
            $timeline->companyid = $cmpid;
            $timeline->packageid = $packageid;
            $timeline->stageid = $timelinetemp->id;
            $timeline->expirydays = $expirydays;
//            $referencedate = HelperServiceProvider::increaseDateByDays($expirydays);
//            $timeline->referencedate = $referencedate;
            $timeline->active = 1;
            $timeline->approved = 1;
//            $cutoffdate = $referencedate;
            $timeline->save();
        }
    }
    public static function updateCmpTimeline($cmpid, $sma2date)
    {
        $cmptimelines = Timeline::getCmpTimeline($cmpid);
        foreach ($cmptimelines as $cmptimeline) {
            $timeline = Timeline::find($cmptimeline->id);
            $expirydays = $timeline->expirydays;
            $referencedate = HelperServiceProvider::increaseDateByDays($expirydays, $sma2date);
            $timeline->referencedate = $referencedate;
            $sma2date = $referencedate;
            $timeline->save();
        }
    }

    public static function getCmpTimeline($cmpid)
    {
        $cmptimeline = Timeline::where('companyid', '=', $cmpid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get();
        return $cmptimeline;
    }
    public static function updateFlashCmpTimeline($cmpid, $sma2date)
    {
        if($sma2date) {
            $cmptimelines = Timeline::getCmpFlashTimeline($cmpid);
            foreach ($cmptimelines as $cmptimeline) {
                $timeline = Timeline::find($cmptimeline->id);
                $expirydays = $timeline->expirydays;
                $referencedate = HelperServiceProvider::increaseDateByDays($expirydays, $sma2date);
                $timeline->referencedate = $referencedate;
                $sma2date = $referencedate;
                $timeline->save();
            }
        }
    }
    public static function updateFlashStage3CmpTimeline($cmpid, $cdregapprovaldate)
    {
        if($cdregapprovaldate) {
            $cmptimeline = Timeline::getCmpFlashStage3Timeline($cmpid);
            $timeline = Timeline::find($cmptimeline->id);
            $expirydays = $timeline->expirydays;
            $referencedate = HelperServiceProvider::increaseDateByDays($expirydays, $cdregapprovaldate);
            $timeline->referencedate = $referencedate;
            $timeline->save();
        }
    }
    public static function getCmpFlashTimeline($cmpid)
    {
        $cmptimeline = Timeline::where('companyid', '=', $cmpid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->take(3)
            ->get();
        return $cmptimeline;
    }

    public static function getCmpFlashStage3Timeline($cmpid)
    {
        $cmptimeline = Timeline::where('companyid', '=', $cmpid)
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->get()
            ->last();
        return $cmptimeline;
    }
    public static function insertActualDate($cmpid, $stageid, $actdate) {
        $timeline = Timeline::where('stageid', '=', $stageid)
            ->where('companyid', '=', $cmpid)
            ->first();
        $acttime = strtotime($actdate);
        $actdate = date('Y-m-d', $acttime);
        $timeline->actualdate = $actdate;
        $timeline->save();
    }

    public static function getStageid($cmpid, $pkgid) {
        $stageid = Timeline::select('stageid')
            ->where('companyid', '=', $cmpid)
            ->where('packageid', '=', $pkgid)
            ->first();
        if($stageid) {
           return $stageid->stageid;
        }
    }
    public static function updateActualdate($stageid, $actdate) {
        $timeline = Timeline::where('id', '=', $stageid)
            ->first();
        $acttime = strtotime($actdate);
        $actdate = date('Y-m-d', $acttime);
        $timeline->actualdate = $actdate;
        $timeline->save();
    }
    public static function getCmptimelineIds($companyid, $packageid, $stageid) {
        $timeline = Timeline::where('companyid', '=', $companyid)
            ->where('packageid', '=', $packageid)
            ->where('stageid', '=', $stageid)
            ->first();
        return $timeline;
    }
    public static function updateFlashActualdate($stageid, $actdate) {
    if($actdate) {
        $timeline = Timeline::where('id', '=', $stageid)
            ->first();
        $acttime = strtotime($actdate);
        $actdate = date('Y-m-d', $acttime);
        $timeline->actualdate = $actdate;
        $timeline->save();
    }
    }
    public static function updateExtensionDate($alldata,$cmpid) {
        $cmptimeline = Timeline::where('companyid', '=', $cmpid)
            ->first();
        $timeline = Timeline::find($alldata['id']);
        $timeline->extension = 1;
        $acttime = strtotime($alldata['coregroupapprovaldate']);
        $actdate = date('Y-m-d', $acttime);
        $timeline->coregroupapprovaldate = $actdate;
        $timeline->expirydays = 180;
        $referencedate = HelperServiceProvider::increaseDateByDays(180, $cmptimeline->referencedate);
        $timeline->referencedate = $referencedate;
        $timeline->save();
    }
}
