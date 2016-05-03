<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Exposure extends Model {

    protected $table = 'exposures';

    public static function approvePending($id) {
        $currentid = Exposure::find($id);
        $oid = $currentid->oid;
        $oldfct = Exposure::find($oid);
        if($oid > 0) {
            $oldfct->exposurerev = $currentid->exposurerev;
            $oldfct->companyid = $currentid->companyid;
            $oldfct->packageid = $currentid->packageid;
            $oldfct->assistancedate = $currentid->assistancedate;
            $oldfct->refdate = $currentid->refdate;
            $oldfct->cutoff = $currentid->cutoff;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }

    public function getActiveData(){
        return Exposure::where('active','=',1)
            ->where('approved','=',1)
            ->get();
    }

    public static function getRules(){
        return array(
            'exposurerev' => 'required|numeric',
            'assistancedate' => 'required|date',
            'refdate' => 'required|date',
        );
    }

    public static function getMessages(){
        return array(
            'exposurerev.required' => 'The Exposure Revision field is required.',
            'assistancedate.required' => 'The Assistance date field is required.',
            'assistancedate.date' => 'The Assistance date is not a valid date.',
            'refdate.required' => 'The Reference date field is required.',
            'refdate.date' => 'The Reference date is not a valid date.',
        );
    }

    public function createExposure($params){
        $datera = strtotime($params['datefa']);
        $date1 = date('Y-m-d',$datera);
        $deteref = strtotime($params['dateref']);
        $date2 = date('Y-m-d',$deteref);
        $this->companyid = $params['companyid'];
        $this->packageid = Session::get('pkgid');
        $this->assistancedate = $date1;
        $this->cutoff = $params['cutoff'];
        $this->refdate = $date2;
        $this->save();
        return $this->id;
    }

    public static function getExposureList(){
        $exposure = DB::table('exposures')
            ->select('id','refdate')
            ->groupBy('refdate')
            ->get();
        return $exposure;
    }

    public static function getByDate($date){
        $cmpid = Session::get('cmpid');
        $exposure = Exposure::select('id')
            ->where('refdate',$date)
            ->where('companyid','=',$cmpid)
            ->get()->toArray();
        return $exposure;
    }

    public static function updateExposure($params){
        $exposure = self::find($params['eid']);
        if($exposure){
            $datera = strtotime($params['datefa']);
            $date = date('Y-m-d',$datera);
            $exposure->cutoff = $params['cutoff'];
            $exposure->assistancedate = $date;
            $exposure->save();
        }
    }

    public static function createAllExposure($data,$date2){
        $exposure = new Exposure();
        $datera = strtotime($data[8]);
        $date1 = date('Y-m-d',$datera);
        $exposure->companyid = Session::get('cmpid');
        $exposure->packageid = Session::get('pkgid');
        $exposure->assistancedate = $date1;
//                    $exposure->cutoff = $params['cutoff'];
        $exposure->refdate = $date2;
        $exposure->save();
        return $exposure->id;
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Relationships
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function company() {
        return $this->belongsTo('App\Models\Company','id');
    }

    public function package() {
        return $this->belongsTo('App\Models\Package','id');
    }

    public function exposuredetails(){
        return $this->hasMany('App\Models\ExposureDetail','id');
    }
}