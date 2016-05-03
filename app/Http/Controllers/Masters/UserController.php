<?php
namespace App;
namespace App\Http\Controllers\Masters;

use App\Models\RelationView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Models\Group;
use App\Models\Department;
use App\Models\Designation;
use App\Models\UserProfile;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
//use App\Models\Group;
use App\Models\Right;
use App\Models\State;
use DB;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//        $groupid = Auth::user()->groupid;
//        $rightid = Group::getRightid($groupid);
//        $rights = Right::getRights($rightid);
//        Session::put('rights',$rights);
//        $deptid = Auth::user()->departmentid;
//        Session::put('deptid',$deptid);
        // get all the nerds
        $user = new User();
        $users = $user
            ->where('active','=',1)
            ->where('approved','=',1)
            ->get();

        // load the view and pass the nerds
        return view('user.index')
            ->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
//        $groupids = Group::lists('name', 'id');
//
//        $departmentids = Department::lists('name', 'id');
//
//        $managerids = User::lists('email', 'id');
//
//        $designationids = Designation::lists('name', 'id');
        $rights = Right::lists('name','id');
        return view('user.create')
            ->with('rights',$rights);
//            ->with('groupids', $groupids)
//            ->with('departmentids', $departmentids)
//            ->with('managerids', $managerids)
//            ->with('designationids', $designationids);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'fname'       => 'required|string|max:50',
            'lname'      => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
            
        );
        $validator = Validator::make(Input::all(), $rules);
//        $pwd = Input::get('password');
//        $cnfpwd = Input::get('password_confirmation');

        // process the login
        if ($validator->fails()) {
            return Redirect::to('user/create')
                ->withInput()
                ->withErrors($validator);
        }
//        else if($pwd != $cnfpwd){
//                return Redirect::to('user/create')
//                ->withInput();
//        }
        else{
//            // store
            $user = new User;
            $user->fname = Input::get('fname');
            $user->mname = Input::get('mname');
            $user->lname = Input::get('lname');
            $user->employeeid = Input::get('employeeid');
//            $user->groupid = Input::get('groupid');
//            $user->departmentid = Input::get('departmentid');
            $user->email = Input::get('email');
            $user->password = bcrypt(Input::get('password'));
            $user->address1 = Input::get('address1');
            $user->address2 = Input::get('address2');
            $user->contact1 = Input::get('contact1');
            $user->contact2 = Input::get('contact2');
            $user->mobile = Input::get('mobile');
            $user->dob = Input::get('dob');
            $user->rightid = Input::get('rightid');
//            $user->managerid = Input::get('managerid');
//            $user->designationid = Input::get('designationid');
            $user->active      = 0;
            $user->approved      = 0;
            $user->save();
            $alldata = Input::all();
            $file = isset($alldata['userimage']) ? $alldata['userimage'] : null;
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $destinationPath = 'dist/img/userprofile';
                $filename = $file->getClientOriginalName();
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $userprofile = new UserProfile();
                    $userprofile->type = $file->getClientMimeType();
//                    $userprofile->name = $filename . '.' . $extension;
                    $userprofile->name = $filename;
//                    $userprofile->extension = $file->getFilename() . '.' . $extension;
//                    $userprofile->content = $file->getFilename() . '.' . $extension;
                    $userprofile->userid = $user->id;
                    $userprofile->save();
                }
            }
            $userprofilename = UserProfile::userprofile();
            Session::put('userprofile',$userprofilename);
            // redirect
            Session::flash('success', 'User Created Successfully !');
            return Redirect::to('user');
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // get the nerd
        $user = User::find($id);

        // show the view and pass the nerd to it
        return view('user.show')
            ->with('user', $user);
    }

    public function profile()
    {


        return view('user.profile');

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $groupids = Group::lists('name', 'id');
//        $departmentids = Department::lists('name', 'id');
        $managerids = User::lists('email', 'id');
        $designationids = Designation::lists('name', 'id');
        $rights = Right::lists('name','id');
        return view('user.edit')
            ->with('user', $user)
            ->with('groupids', $groupids)
//            ->with('departmentids', $departmentids)
            ->with('managerids', $managerids)
            ->with('designationids', $designationids)
            ->with('rights', $rights);

    }
    public function update($id)
    {
        $rules = array(
            'fname'       => 'required|string|max:50',
            'lname'      => 'required|string|max:50',
             'email' => 'email|max:255',
//            'password' => 'required|min:6'
            
        );
        $validator = validator(Input::all(), $rules);
          if ($validator->fails()) {
            return Redirect::to('user/' . $id . '/edit')
                ->withErrors($validator);
        } else {
            $user = User::find($id);
            $user->fname = Input::get('fname');
            $user->mname = Input::get('mname');
            $user->lname = Input::get('lname');
            $user->employeeid = Input::get('employeeid');
//            $user->groupid = Input::get('groupid');
            $user->email = Input::get('email');
//            $user->departmentid = Input::get('departmentid');
            $user->address1 = Input::get('address1');
            $user->address2 = Input::get('address2');
            $user->contact1 = Input::get('contact1');
            $user->contact2 = Input::get('contact2');
            $user->mobile = Input::get('mobile');
            $user->dob = Input::get('dob');
            $user->rightid = Input::get('rightid');
//            $user->managerid = Input::get('managerid');
//            $user->designationid = Input::get('designationid');
            $user->save();
              $alldata = Input::all();
//        dd($alldata);
              $file = isset($alldata['userimage']) ? $alldata['userimage'] : null;
              if ($file) {
                  $extension = $file->getClientOriginalExtension();
                  $destinationPath = 'dist/img/userprofile';
                  $filename = $file->getClientOriginalName();
                  $upload_success = $file->move($destinationPath, $filename);
                  if ($upload_success) {
                      $userprofile = UserProfile::where('userid', '=', $id)
                          ->first();
                      if (!isset($userprofile)) {
                          $userprofile = new UserProfile();
                      }
                      $userprofile->type = $file->getClientMimeType();
                      $userprofile->name = $filename;
                      $userprofile->userid = $id;
                      $userprofile->save();
                  }

              }
              $userprofilename = UserProfile::userprofile();
            Session::put('userprofile',$userprofilename);
            Session::flash('message', 'User Updated Successfully !!');
            return Redirect::to('user/'.$id.'/edit');
        }
    }
    public function destroy($id)
    {
        $table = new User();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = User::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'User deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return Redirect::to('user');
    }

    public function getPendingUser()
    {
        // Show All Pending User

        $user = new User;
        $users = $user
            ->where('active','=',0)
            ->where('approved','=',0)
            ->get();

        return view('user.pending')
            ->with('users', $users);
    }
    public function postPendingUser($id)
    {
        // Show All Pending User
       
        $user = User::find($id);
        $user->active = 1;
        $user->approved = 1;
        $user->save();
        // redirect
        Session::flash('success', 'User approved Successfully !!');
        return redirect::to('user/pending');
    }
    public function reject($userid)
    {
        // Show All Pending User

        $rejectuser = User::find($userid);
        $rejectuser->active = 1;
        $rejectuser->approved = 0;
        $rejectuser->save();
        Session::flash('error', 'User rejected Successfully !!');
        return redirect::to('user/pending');
    }

}
