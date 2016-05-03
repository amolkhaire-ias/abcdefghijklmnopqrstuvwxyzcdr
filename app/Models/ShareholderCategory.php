<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShareholderCategory extends Model
{
    protected $table = 'shareholdercategories';

    public static function getDataFromShareholder()
    {
        return ShareholderCategory::all()->where('active',1)->where('approved',1);
    }

    public function createShareholder($params)
    {
        $this->companyid = $params['name'];
//        $this->packageid = $params['package'];
        $this->code = $params['code'];
        $this->category = $params['category'];
        $this->rootid = $params['parent'];
        $this->active = 0;
        $this->approved = 0;
        $this->save();
    }

    public function updateShareholder($params)
    {
        $this->companyid = $params['companyid'];
        $this->packageid = $params['packageid'];
        $this->code = $params['code'];
        $this->category = $params['category'];
        $this->rootid = $params['rootid'];
        $this->active = $params['active'];
        $this->approved = $params['approved'];
        $this->save();
    }

    public static function getPendingDataFromShareholder()
    {
        return ShareholderCategory::all()->where('active',0)->where('approved',0);
    }

    public function createNewShareholder($params){
        $this->oid = $params['id'];
        $this->companyid = $params['name'];
//        $this->packageid = $params['package'];
        $this->code = $params['code'];
        $this->rootid = $params['parents'];
        $this->category = $params['category'];
        $this->active = 0;
        $this->approved = 0;
        $this->save();
    }

    public static function getActiveApproved(){

    }
}
