<?php 

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class State extends Model {

    public static function getAllState() {
        $state = State::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('statename','ASC')
            ->lists('statename', 'id');
        return $state;
    }
   public function cities(){
         return $this->has_many('City');
      }
      
   public function country(){
         return $this->belongs_to('Country');
      }
      
  public static function whereCountry($id){
        return DB::table('States')
        ->select('statecode', 'statename')
        ->where('countrycode', '=', $id)
        ->get();
    }
    public static function approvePending($id) {
        $currentid = State::find($id);
        $oid = $currentid->oid;
        $oldfct = State::find($oid);
        if($oid > 0) {
            $oldfct->countryid = $currentid->countryid;
            $oldfct->statecode = $currentid->statecode;
            $oldfct->statename = $currentid->statename;
            $oldfct->save();
            $currentid->delete();
        }else{
            $currentid->active = '1';
            $currentid->approved = '1';
            $currentid->save();
        }
    }
}