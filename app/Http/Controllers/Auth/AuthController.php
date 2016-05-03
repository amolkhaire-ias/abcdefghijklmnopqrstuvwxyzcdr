<?php

namespace App\Http\Controllers\Auth;

use App\Models\Right;
use App\Models\UserProfile;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    private $redirectTo = 'home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
       
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    public function getLogout()
    {
        $this->logout();
        $error = Session::get('pendingerror');
        Session::flush();
        if($error){
            Session::put('pendingerror',$error);
        }
        return redirect('/');
    }
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }
        $authuser = Auth::user();
        if($authuser->active == 0 && $authuser->approved == 0) {
            Session::put('pendingerror','Your Account is not Approved. Please contact with Admin.');
            return redirect::to('auth/logout');
        }
        $rights = Right::find($authuser->rightid);
        Session::put('right',$rights->right);
        Session::put('userid',$rights->id);
        $userprofilename = UserProfile::userprofile();
        Session::put('userprofile',$userprofilename);
        return redirect()->intended($this->redirectPath());
    }
}
