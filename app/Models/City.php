<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class City extends Model {
    public static function getAllCity() {
        $city = City::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('cityname','ASC')
            ->lists('cityname', 'id');
        return $city;
    }
   public function state(){
         return $this->belongs_to('State');
      }
      
   public function country(){
         return $this->belongs_to('Country');
      }

    public static function approvePending($id) {
        $currentid = City::find($id);
        $oid = $currentid->oid;
        $oldfct = City::find($oid);
        if($oid > 0) {
            $oldfct->countryid = $currentid->countryid;
            $oldfct->stateid = $currentid->stateid;
            $oldfct->citycode = $currentid->citycode;
            $oldfct->cityname = $currentid->cityname;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }

}