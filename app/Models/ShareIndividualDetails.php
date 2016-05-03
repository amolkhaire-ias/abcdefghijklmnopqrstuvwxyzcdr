<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShareIndividualDetails extends Model
{
    protected $table = 'sharedetails';

    public function createIndividualEntry($params){
        $this->shareholdername = $params['shareholdername'];
        $this->tnumofshare = $params['tnoshare'];
        $this->ttotalpercent = $params['tgtotal'];
        $this->snumofshare = $params['snoshare'];
//        $this->saspercent = $params['spercent'];
        $this->stotalpercent = $params['sgtotal'];
        $this->save();
    }

    public static function getIndividualShares(){
        return self::all();
    }

}
