<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class TimelineTemplate extends Model
{
    protected $table = 'timelinetemplates';

    public static function createTimelineTemp($timelinetempdata) {
        $timelinetemp = new TimelineTemplate();
        $timelinetemp->stage = $timelinetempdata['stage'];
        $timelinetemp->expirydays = $timelinetempdata['expirydays'];
        $timelinetemp->highexposure = $timelinetempdata['highexposure'];
        $timelinetemp->timelinemasterid = $timelinetempdata['timelinemasterid'];
        $timelinetemp->remarks = $timelinetempdata['remarks'];
        $timelinetemp->srno = $timelinetempdata['srno'];
//        $timelinetemp->revid = $timelinetempdata['revid'];
        $timelinetemp->save();
    }
    public static function getAllTemplate() {
        return TimelineTemplate::orderBy('timelinemasterid','ASC')
            ->orderBy('srno','ASC')
            ->get();
    }

    public static function findById($id) {
        $timelinetemp = TimelineTemplate::find($id);
        return $timelinetemp;
    }

    public static function updateById($timelinetempdata) {
        $timelinetemp = TimelineTemplate::find($timelinetempdata['timelineid']);
        $timelinetemp->stage = $timelinetempdata['stage'];
        $timelinetemp->srno = $timelinetempdata['srno'];
        $timelinetemp->expirydays = $timelinetempdata['expirydays'];
        $timelinetemp->highexposure = $timelinetempdata['highexposure'];
        $timelinetemp->timelinemasterid = $timelinetempdata['timelinemasterid'];
        $timelinetemp->remarks = $timelinetempdata['remarks'];
//        $timelinetemp->revid = $timelinetempdata['revid'];
        $timelinetemp->save();
    }

    public static function getUniqueSysRev() {
        return TimelineTemplate::select('timelinemasterid')
            ->distinct()
            ->orderBy('timelinemasterid','ASC')
            ->get();
    }
    public static function getAllStages($highexposure, $timelinemasterid) {
        $timelinetemp = TimelineTemplate::where('highexposure', '=', $highexposure)
            ->where('timelinemasterid', '=', $timelinemasterid)
            ->orderBy('srno','ASC')
            ->get();
        return $timelinetemp;
    }
    public static function getUniqueSysRevByHexpo($highexposer) {
        $sysrevs = DB::table('timelinetemplates')
            ->where('timelinetemplates.highexposure','=',$highexposer)
            ->join('timelinemasters','timelinemasters.id','=','timelinetemplates.timelinemasterid')
            ->select('timelinemasters.name','timelinetemplates.timelinemasterid')
            ->distinct()
            ->orderBy('timelinemasterid','ASC')
            ->get();
        return $sysrevs;
    }
    public static function getSysRevByHexpo($highexposer) {
        $sysrevs =  TimelineTemplate::select('timelinemasterid')
            ->where('highexposure','=',$highexposer)
            ->get();
        return $sysrevs;
    }
    public static function getStageHighexpo($stageid) {
        $highexpo = TimelineTemplate::find($stageid);
        if($highexpo) {
           return $highexpo->highexposure;
        }
    }
    public static function getStageTempIds($highexposure, $timelinemasterid) {
        $timelinetemplates = TimelineTemplate::where('highexposure','=',$highexposure)
            ->where('timelinemasterid','=',$timelinemasterid)
            ->get();
       return $timelinetemplates;
    }

}
