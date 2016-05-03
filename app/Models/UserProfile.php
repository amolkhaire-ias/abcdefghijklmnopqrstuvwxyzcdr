<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class UserProfile extends Model

{
    protected $table = 'userprofile';


    public static function userprofile()
    {
        $userprofile = UserProfile::where ('userid','=',Auth::user()->id)
            ->first();
        if($userprofile){
            return $userprofile->name;
        }
    }
}
