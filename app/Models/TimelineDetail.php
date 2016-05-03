<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TimelineDetail extends Model
{
    protected $table = 'timelinedetails';
    public function company() {
        return $this->belongsTo('App\Models\Company','companyid');
    }
    public function user() {
        return $this->belongsTo('App\User','userid');
    }
    public static function createCmpTimelineDetail($cmpid, $packageid, $timelinemasterid, $timelinename,$highexposure) {
        $timelinedetail = new TimelineDetail();
        $timelinedetail->timelinename = $timelinename;
        $timelinedetail->companyid = $cmpid;
        $timelinedetail->packageid = $packageid;
        $timelinedetail->timelinemasterid = $timelinemasterid;
        $timelinedetail->highexposure = $highexposure;
        $timelinedetail->userid =  Auth::user()->id;
        $timelinedetail->creationdate =  Carbon::now();
        $timelinedetail->save();
    }
    public static function gethighexpoByCmpid($cmpid, $pkgid) {
        $timelinedetail = TimelineDetail::where('companyid', '=', $cmpid)
            ->where('packageid','=',$pkgid)
            ->first();
        return $timelinedetail;
    }

    public static function getreporthighexpoByCmpid($cmpid) {
        $timelinedetail = TimelineDetail::where('companyid', '=', $cmpid)
            ->first();
        return $timelinedetail;
    }
}
