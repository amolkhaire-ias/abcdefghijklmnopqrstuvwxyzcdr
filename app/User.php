<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    public function user()
//    {
//        return $this->hasOne('App\User');
//    }
//    public function company()
//    {
//        return $this->belongsToMany('App\Models\Company');
//    }
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getUserFname() {
        $userfnames = User::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('fname', 'id');
        return $userfnames;
    }
    public static function getUserLname() {
        $userlnames = User::where('active', '=', 1)
            ->where('approved', '=', 1)
            ->lists('lname', 'id');
        return $userlnames;
    }

    public static function getUserFnameLname() {
        $users = User::select('id','fname','lname')
            ->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->orderBy('fname','ASC')
            ->get();
        return $users;
    }
    public static function getIdByGroupDept($groupid,$deptid){
        $userid = User::select('id')
            ->where('groupid','=',$groupid)
            ->where('departmentid','=',$deptid)
            ->get();
        return $userid;
    }

//    public function instituted()
//    {
//        return $this->hasManyThrough('App\Models\Institute', 'App\Models\Company', 'relationshipmgrid', 'companyid');
//    }
}
