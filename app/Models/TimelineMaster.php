<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineMaster extends Model
{
    protected $table = 'timelinemasters';

    public static function getSysRevByHexpo($highexposer) {
        $sysrevs =  TimelineMaster::where('highexposure','=',$highexposer)
            ->get();
        return $sysrevs;
    }

    public static function getTimelineMasterByName() {
        $sysrevs =  TimelineMaster::lists('name','id');
        return $sysrevs;
    }

}
