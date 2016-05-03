<?php

namespace App\Http\Controllers;
use App\Models\CompanyExchnage;
use App\Models\CompanyType;
use App\Models\Industry;

use App\Models\Package;
use App\Models\RelationView;
use App\User;
use Illuminate\Support\Facades\Input;
use Session;
use App\Http\Requests;
use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $userid = Session::get('userid');
        $companies = Company::where('active','=',1)
            ->where('approved','=',1)
//            ->where('relationshipmgrid','=',$userid)
            ->orderBy('name', 'ASC')
            ->lists('name', 'id');
        Session::forget('companyid');
        $company = new Company();
        $companydetails = $company->getAllCompanyDetail();
        $managerfnames = User::getUserFname();
        $managerlnames = User::getUserLname();
        return view('home')
            ->with('companydetails', $companydetails)
            ->with('managerfnames', $managerfnames)
            ->with('managerlnames', $managerlnames)
            ->with('companies',$companies);
    }
    public function companysession(Request $request, $id){
        Session::put('cmpid', $id);
        $cmpname = Company::getCmpNameById($id);
        Session::put('cmpname', $cmpname);
        $pkgid = Package::getPkgid($id);
        Session::put('pkgid',$pkgid);
        return redirect('dashboard');
    }
    public function dashboard(Request $request){

         $cmpid = Session::get('cmpid');
        $company = Company::find($cmpid);
        $companytype = CompanyType::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $industry = Industry::where ('active','=',1)-> where ('approved','=',1)-> lists('name', 'id');
        $cmpexchange = CompanyExchnage::where ('active','=',1)-> where ('approved','=',1)-> where ('companyid','=',$cmpid)->get();

            return view('dashboard')
                ->with('company',$company)
                ->with('companytype',$companytype)
                ->with('industry',$industry)
                ->with('cmpexchange',$cmpexchange);

    }
    public function cmpSearchAjax() {
        $userid = Session::get('userid');
        $startswith = Input::get('name_startsWith');
        $companies = Company::where ('active','=',1)
            ->where('approved','=',1)
//            ->where('relationshipmgrid','=',$userid)
            ->where('name','like','%'.$startswith.'%')
            ->get();
//        $data = array();
//        dd($data);
        echo json_encode($companies);
    }


}
