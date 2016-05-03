<?php

namespace App\Http\Controllers\Company;

use App\Models\AccountCategory;
use App\Models\Activity;
use App\Models\AddressCmpMap;
use App\Models\BorrowerClass;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyExchnage;
use App\Models\CompanyType;
use App\Models\Country;
use App\Models\Exchange;
use App\Models\HoldingStatus;
use App\Models\Industry;
use App\Models\Package;
use App\Models\Sector;
use App\Models\SectorCompMaps;
use App\Models\State;
use App\Models\Status;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
//        $cmpid = Session::get('cmpid');
//        if(!$cmpid) {
//            Redirect::to('dashboard')->send();
//        }
    }

    public function index()
    {
        Session::forget('companyid');
        $company = new Company();
        $companydetails = $company->getAllCompanyDetail();

        $managerfnames = User::getUserFname();
        $managerlnames = User::getUserLname();
        return view('company/index')
            ->with('companydetails', $companydetails)
            ->with('managerfnames', $managerfnames)
            ->with('managerlnames', $managerlnames);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Session::forget('companyid');
        $update = 0;
        $pendview = 0;
        return $this->viewCreateCompany($update, $pendview);
    }

    public function update($cmpid,$isedit = 0)
    {
        Session::put('companyid', $cmpid);
        $update = 1;
        $pendview = 0;
        $company = Company::find($cmpid);
        $pkgdata = Package::getPkgByCmpid($cmpid);
        $seccmpmapsecids = SectorCompMaps::listCmpidBySectId($cmpid);
        $exgtyperate = CompanyExchnage::listRateByExgid($cmpid);
        $exgtypedate = CompanyExchnage::listDateByExgid($cmpid);
        return $this->viewCreateCompany($update, $pendview, $company, $seccmpmapsecids, $exgtyperate, $exgtypedate, $pkgdata, $isedit);
    }

    public function viewPendingToApprove($cmpid)
    {
        Session::put('companyid', $cmpid);
        $update = 1;
        $pendview = 1;
        $company = Company::find($cmpid);
        $seccmpmapsecids = SectorCompMaps::listCmpidBySectId($cmpid, $pendview);
        $exgtyperate = CompanyExchnage::listRateByExgid($cmpid, $pendview);
        $exgtypedate = CompanyExchnage::listDateByExgid($cmpid, $pendview);
        $pkgdata = Package::getPkgByCmpid($cmpid,$pending = 1);
        return $this->viewCreateCompany($update, $pendview, $company, $seccmpmapsecids, $exgtyperate, $exgtypedate, $pkgdata);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sectorids = Input::get('sectoridarray') ? explode(",", Input::get('sectoridarray')) : [];
        $exchangetypeids = Input::get('exchangeidarray') ? explode(",", Input::get('exchangeidarray')) : [];
        $companyData = Input::all();
        $submittype = $companyData['submittype'];
        $oldcompanyid = $companyData['pendview'] != '' ? $companyData['pendview'] : $companyData['updateid'];
        $rules = [];
        $rules['name'] = 'required|string|max:60';
        $rules['packagedate'] = 'date';
        if ($companyData['primarysectorid'] == 3) {
            $rules['sectordesc'] = 'required|string:';
        }
        if ($companyData['primarysectorid'] != 3) {
            $companyData['sectordesc'] = '';
        }
        if ($companyData['holdingstatusid'] == 1 && isset($companyData['exchnageid1'])) {
            $rules['nsedate'] = 'required|date:';
            $rules['nserate'] = 'required|regex: /^\d+(\.\d+)?$/i';
        }
        if ($companyData['holdingstatusid'] == 1 && isset($companyData['exchnageid2'])) {
            $rules['bsedate'] = 'required|date:';
            $rules['bserate'] = 'required|regex: /^\d+(\.\d+)?$/i';
        }
        if ($companyData['willfuldefaulter'] == 1) {
            $rules['willfuldefaulterdate'] = 'required|date:';
            $rules['willfuldefaulterdesc'] = 'required|string:';
        }
        if ($companyData['bifr'] == 1) {
            $rules['bifrdate'] = 'required|date:';
        }
        $messages = array('nserate.regex' => 'The NSE Rate must be valid decimal number',
            'nserate.required' => 'The NSE Rate field is required.',
            'nsedate.required' => 'The NSE Date field is required.',
            'bserate.regex' => 'The BSE Rate must be valid decimal number',
            'bserate.required' => 'The BSE Rate field is required.',
            'bsedate.required' => 'The BSE Date field is required.');
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to($submittype == "update" ? 'company/update/' . $oldcompanyid : 'company/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            if ($submittype == "create") {
                $companyid = Company::createCompany($companyData);
                SectorCompMaps::storeMultiSector($sectorids, $companyid);
                CompanyExchnage::createCompanyExchange($companyData, $companyid, $exchangetypeids);
                Package::createPackage($companyid, $companyData);
                Session::put('companyid', $companyid);
                Session::flash('success', 'Company Created Successfully and sent for approval!!');
                return Redirect::to('company/companyaddress/0');
            } else if ($submittype == "update") {
                $companyid = Company::createCompany($companyData, $oldcompanyid);
                SectorCompMaps::storeMultiSector($sectorids, $companyid, $oldcompanyid);
                CompanyExchnage::createCompanyExchange($companyData, $companyid, $exchangetypeids, $oldcompanyid);
                Package::updatePackage($companyid, $oldcompanyid, $companyData);
                if ($companyData['pendview'] != '') {
                    Session::flash('success', 'Company Changes has been Saved. Please Click on Approve Button!!!');
                } else {
                    Session::flash('success', 'Changes has been sent for Approval!!');
                }
                return $companyData['pendview'] != '' ? redirect('company/showtoapprove/' . $companyData['updateid']) : redirect('company');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getCompanyAddress($isedit = 0)
    {
        $companyid = Session::get('companyid');

        $countries = Country::getAllCountry();
        $states = State::getAllState();
        $cities = City::getAllCity();
        $addresscmpmaps = AddressCmpMap::getCompanyAddress($companyid);
        return view('company.address')
            ->with('countries', $countries)
            ->with('states', $states)
            ->with('cities', $cities)
            ->with('addresscmpmaps', $addresscmpmaps)
            ->with('isedit', $isedit);
    }

    public function postCompanyAddress()
    {
        $cmpaddressdata = Input::all();
        $rules = array(
            'contactperson' => 'required|string|max:100',
            'contactnumber' => 'required|regex: /^[+]?[0-9]?[0-9]?\d{10}$/',
            'emailid' => 'required|email',
            'address1' => 'required|string|max:100',
            'address2' => 'string|max:100',
            'countryid' => 'required',
            'stateid' => 'required',
            'cityid' => 'required'
        );
        $messages = array('countryid.required' => 'The Country field is required.',
            'stateid.required' => 'The State field is required.',
            'cityid.required' => 'The City field is required.');
        $validator = Validator::make(Input::all(), $rules,$messages);
        if ($validator->fails()) {
            return Redirect::to('company/companyaddress')
                ->withInput()
                ->withErrors($validator);
        } else {
            $companyid = Session::get('companyid');
            AddressCmpMap::createCompanyAddress($companyid, $cmpaddressdata);
            Session::flash('success', 'Address Created Successfully and Send For Approval !!!');
            return Redirect::to('company/companyaddress/0');
        }
    }

    public function stateDetails()
    {
        $countryId = Input::get('countryId');
        $state = new State();
        $states = $state->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->where('countryid', '=', $countryId)->get();
        return $states;
    }

    public function cityDetails()
    {
        $stateId = Input::get('stateId');
        $city = new City();
        $cities = $city->where('active', '=', 1)
            ->where('approved', '=', 1)
            ->where('stateid', '=', $stateId)->get();
        return $cities;
    }

    public function getPendingCompany()
    {
        Session::forget('companyid');
        $pendcmps = Company::getAllPendingCompany();
        $user = new User();
        $managerfnames = $user->getUserFname();
        $managerlnames = $user->getUserLname();
        return view('company/pending')
            ->with('pendcmps', $pendcmps)
            ->with('managerfnames', $managerfnames)
            ->with('managerlnames', $managerlnames);
    }

    public function postPendingCompany($cmpid)
    {
        $cmpoid = Company::approvePendingCompany($cmpid);
        SectorCompMaps::approvePendingSecCompMaps($cmpid, $cmpoid);
        CompanyExchnage::approvePendingCompExg($cmpid, $cmpoid);
        Package::approvePendingPkg($cmpid,$cmpoid);
        Session::flash('success', 'Company Approved Successfully !!!');
        return redirect('company/pending');
    }

    public function rejectPendingCompany($cmpid)
    {
        Company::rejectPendingCompany($cmpid);
        SectorCompMaps::rejectPendingSecCompMaps($cmpid);
        CompanyExchnage::rejectPendingCompExg($cmpid);
        Session::flash('error', 'Company Rejected Successfully !!!');
        return redirect('company/pending');
    }

    public function getPendingAddr()
    {
        $addrcmpmap = new AddressCmpMap();
        $addrcmpmaps = $addrcmpmap->getAllPendAddress();
        unset($addrcmpmap);
        return view('company/pendingaddress')
            ->with('addrcmpmaps', $addrcmpmaps);
    }

    public function editPendingAddr($addrid, $pendview)
    {
        $countries = Country::getAllCountry();
        $states = State::getAllState();
        $cities = City::getAllCity();
        $cmpaddr = AddressCmpMap::getCmpAddrById($addrid);
        if ($pendview) {
            Session::flash('message', 'Changes Saved Sucessfully. Click on Approve to Approve Address');
        } else {
            Session::flash('message', 'Changes Sent for Approval.');
        }
        return view('company.editaddress')
            ->with('countries', $countries)
            ->with('states', $states)
            ->with('cities', $cities)
            ->with('cmpaddr', $cmpaddr)
            ->with('pendview', $pendview);
    }

    public function postPendingAddr()
    {
        $cmpaddressdata = Input::all();
        $pendview = $cmpaddressdata['pendview'];
        $addrid = $cmpaddressdata['addrid'];
        $rules = array(
            'contactperson' => 'required|string|max:100',
            'contactnumber' => 'required',
            'emailid' => 'required|email',
            'address1' => 'required|string|max:100',
            'address2' => 'string|max:100'
        );
        $validator = validator(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('company/editpendingaddr/' . $addrid . '/' . $pendview)
                ->withErrors($validator);
        } else {
            AddressCmpMap::updateCmpAddress($cmpaddressdata, $addrid, $pendview);
            Session::flash('message', 'Successfully updated facilities!');
            return Redirect::back();
        }
    }

    public function approvePendingAddr($addrid)
    {
        $apprpendaddr = AddressCmpMap::approvePendingAddr($addrid);
        if ($apprpendaddr) {
            Session::flash('message', 'Company Address Approved Sucessfully!!');
            return Redirect('company/pendingaddr');
        }
    }

    public function rejectPendingAddr($addrid)
    {
        AddressCmpMap::rejectPendingAddr($addrid);
        Session::flash('error', 'Company Address Rejected Sucessfully!!');
        return Redirect('company/pendingaddr');
    }

    public function viewCreateCompany($update, $pendview, $company = null, $seccmpmapsecids = null, $exgtyperate = null, $exgtypedate = null, $pkgdata = null, $isedit =0)
    {
        $sectors = Sector::listSectorById();//req
        $mutisectors = Sector::getAllSectorByName();
        $industries = Industry::listIndustriesById();
        $companytypes = CompanyType::listCompanyTypesById();//req
        $holdingstatus = HoldingStatus::listHoldingStatusById();//req
        $accountcategories = AccountCategory::listAccountCategoriesById();
        $borrowerclassesname = BorrowerClass::listBorrowerClassById();
        $borrowerclassesdesc = BorrowerClass::listBorrowerClassDescById();
        $activities = Activity::listActivityNameById();
        $exchanges = Exchange::getAllExchanges();//req
        $users = User::getUserFnameLname();
        $status = Status::getStatus();
        return view('company.createcompany')
            ->with('sectors', $sectors)
            ->with('industries', $industries)
            ->with('companytypes', $companytypes)
            ->with('holdingstatus', $holdingstatus)
            ->with('accountcategories', $accountcategories)
            ->with('borrowerclassesname', $borrowerclassesname)
            ->with('borrowerclassesdesc', $borrowerclassesdesc)
            ->with('activities', $activities)
            ->with('exchanges', $exchanges)
            ->with('mutisectors', $mutisectors)
            ->with('users', $users)
            ->with('status', $status)
            ->with('update', $update)
            ->with('company', $company)
            ->with('seccmpmapsecids', $seccmpmapsecids)
            ->with('exgtyperate', $exgtyperate)
            ->with('exgtypedate', $exgtypedate)
            ->with('pendview', $pendview)
            ->with('pkgdata', $pkgdata)
            ->with('isedit', $isedit);
    }
}
