<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Country extends Model {
    public static function getAllCountry() {
        $country = Country::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('countryname','ASC')
            ->lists('countryname', 'id');
            return $country;
    }
   public function states(){
         return $this->has_many('State');
      }
      
   public function cities(){
         return $this->has_many('City');
      }

    public static function approvePending($id) {
        $currentid = Country::find($id);
        $oid = $currentid->oid;
        $oldfct = Country::find($oid);
        if($oid > 0) {
            $oldfct->countrycode = $currentid->countrycode;
            $oldfct->countryname = $currentid->countryname;
            $oldfct->countrydescription = $currentid->countrydescription;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}