<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShareMoreThanOnePercent extends Model
{
    protected $table = 'sharesaboveone';

    public function createIndividualEntry($params){
        $this->shareholdername = $params['shareholdername'];
        $this->numofshare = $params['noofshare'];
        $this->sharepercent = $params['tpercent'];
        $this->save();
    }

    public static function getIndividualShares(){
        return self::all();
    }

}
