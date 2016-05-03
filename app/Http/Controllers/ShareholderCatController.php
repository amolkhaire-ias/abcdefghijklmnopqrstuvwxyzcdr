<?php

namespace App\Http\Controllers;

use App\Models\ShareholderCat;
use App\Models\Company;
use App\Models\ShareholderCategory;
use App\Models\ShareIndividualDetails;
use App\Models\ShareMoreThanOnePercent;
use App\Models\ShareThanOnePercent;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\Redirect;
class ShareholderCatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $cmpid = Session::get('cmpid');
        if(!$cmpid) {
            Redirect::to('dashboard')->send();
        }
    }

    public function index()
    {
        $cmpid = Session::get('cmpid');
        $company = Company::find($cmpid);
        $shareholderData = ShareholderCat::shareholderJoinIndex();
        $shareholderArray = $this->getRetrievedData($shareholderData);
        return view('shareholdercat.index')
//            ->with('shareholderData', $shareholderData)
            ->with('company', $company)
            ->with('shareArray', $shareholderArray ? $shareholderArray : 0);
    }

    public function createShareholder()
    {
        $shareholderData = ShareholderCat::shareholderJoinIndex();
        $shareholderArray = $this->getRetrievedData($shareholderData);
        return view('shareholdercat.create')
            ->with('shareholderData', $shareholderData)
            ->with('shareArray', $shareholderArray);
    }

    public function store(Request $request)
    {
        $params = $request->all();
        unset($params['_token']);
        $tempArray = array_chunk($params,7);
        foreach($tempArray as $array){
            $shareSaveArray = $this->getChunkArray($array);
            $shareholderCat = new ShareholderCat();
            $shareholderCat->createShareholder($shareSaveArray);
        }
        return redirect('shareholdercat');
    }

    public function updateShareholderDetails(){
        $shareholderData = ShareholderCat::shareholderJoin();
        $shareholderArray = $this->getRetrievedData($shareholderData);
        return view('shareholdercat.edit')
            ->with('shareholderData', $shareholderData)
            ->with('shareArray', $shareholderArray);
    }

    public function updateDetails(Request $request)
    {
        $params = $request->all();
        unset($params['_token']);
        $tempArray = array_chunk($params,7);
        foreach($tempArray as $array){
            $shareSaveArray = $this->getChunkArray($array);
            ShareholderCat::updateShareholder($shareSaveArray['sdid'],$shareSaveArray);
        }
       return redirect('shareholdercat');
    }

    public function approveShareholderDetails()
    {
        $shareholderData = ShareholderCat::shareholderNonApproveJoin();
        $shareholderArray = array();
        foreach($shareholderData as $e){
            $shareholderArray[$e->sid] = array('rootid'=> $e->rootid,'category' => $e->category,
                'codeId'=> $e->code,'id' => $e->sid, 'shareholdernum'=> isset($e->shareholdernum) ? $e->shareholdernum : "",
                'numofshares'=> isset($e->numofshares) ? $e->numofshares : "",
                'totalshares'=> isset($e->totalshares) ? $e->totalshares : "",
                'dematerializeform' => isset($e->dematerializeform) ? $e->dematerializeform : "",
                'percentage'=> isset($e->percentage) ? $e->percentage : "",
                'sdid' => $e->id);
        }
        return view('shareholdercat.pending')
            ->with('shareholderData', $shareholderData)
            ->with('shareArray', $shareholderArray);
    }

    public function update()
    {
        $shareholderData = ShareholderCat::shareholderNonApproveJoin();
        $shareholderArray = array();
        foreach($shareholderData as $e){
            $shareholderArray[$e->sid] = array('rootid'=> $e->rootid,'category' => $e->category,
                'codeId'=> $e->code,'id' => $e->sid, 'shareholdernum'=> isset($e->shareholdernum) ? $e->shareholdernum : "",
                'numofshares'=> isset($e->numofshares) ? $e->numofshares : "",
                'totalshares'=> isset($e->totalshares) ? $e->totalshares : "",
                'dematerializeform' => isset($e->dematerializeform) ? $e->dematerializeform : "",
                'percentage'=> isset($e->percentage) ? $e->percentage : "",
                'sdid' => $e->id);
        }
        return view('shareholdercat.update')
            ->with('shareholderData', $shareholderData)
            ->with('shareArray', $shareholderArray);
    }

    public function reject()
    {
        $shareholderData = ShareholderCat::shareholderNonApproveJoin();
        $shareholderArray = array();
        foreach($shareholderData as $e){
            $shareholderArray[$e->sid] = array('rootid'=> $e->rootid,'category' => $e->category,
                'codeId'=> $e->code,'id' => $e->sid, 'shareholdernum'=> isset($e->shareholdernum) ? $e->shareholdernum : "",
                'numofshares'=> isset($e->numofshares) ? $e->numofshares : "",
                'totalshares'=> isset($e->totalshares) ? $e->totalshares : "",
                'dematerializeform' => isset($e->dematerializeform) ? $e->dematerializeform : "",
                'percentage'=> isset($e->percentage) ? $e->percentage : "",
                'sdid' => $e->id);
        }
        return view('shareholdercat.reject')
            ->with('shareholderData', $shareholderData)
            ->with('shareArray', $shareholderArray);
    }

    public function getRetrievedData($shareholderData){
        $shareholderArray = array();
        foreach($shareholderData as $e){
            $shareholderArray[$e->sid] = array('rootid'=> $e->rootid,'category' => $e->category,
                'codeId'=> $e->code,'id' => $e->sid, 'shareholdernum'=> isset($e->shareholdernum) ? ($e->active) && ($e->approved) ? $e->shareholdernum:"":"",
                'numofshares'=> isset($e->numofshares)? ($e->active) && ($e->approved) ?$e->numofshares:"":"",
                'totalshares'=> isset($e->totalshares) ? ($e->active) && ($e->approved) ?$e->totalshares:"": "",
                'dematerializeform' => isset($e->dematerializeform) ? ($e->active) && ($e->approved) ? $e->dematerializeform:"" : "",
                'percentage'=> isset($e->percentage)? ($e->active) && ($e->approved) ?$e->percentage:"":"",
                'sdid' => $e->id);
        }
        return $shareholderArray;
    }

    public function rejectPending(Request $request)
    {
        $params = $request->all();
        unset($params['_token']);
        $tempArray = array_chunk($params,7);
        foreach($tempArray as $array) {
            $shareSaveArray = $this->getChunkArray($array);
            ShareholderCat::rejectPendingCategories($shareSaveArray['sdid']);
        }
        return redirect('shareholdercat/pending');
    }

    public function updatePending(Request $request)
    {
        $params = $request->all();
        unset($params['_token']);
        $tempArray = array_chunk($params,7);
        foreach($tempArray as $array) {
            $shareSaveArray = $this->getChunkArray($array);
            ShareholderCat::updatePendingCategories($shareSaveArray);
        }
        return redirect('shareholdercat/pending');
    }

    public function approve(Request $request)
    {
        $params = $request->all();

        unset($params['_token']);
        $tempArray = array_chunk($params,7);
        foreach($tempArray as $array){
            $shareSaveArray = $this->getChunkArray($array);
            $addrcmpmap = ShareholderCat::find($shareSaveArray['sdid']);
            $oid = $addrcmpmap->oid;
            if($oid > 0) {
                $oldaddrcmpmap = ShareholderCat::find($oid);
                $oldaddrcmpmap->categoryid = $addrcmpmap->categoryid;
                $oldaddrcmpmap->shareholdernum = $addrcmpmap->shareholdernum;
                $oldaddrcmpmap->totalshares = $addrcmpmap->totalshares;
                $oldaddrcmpmap->dematerializeform = $addrcmpmap->dematerializeform;
                $oldaddrcmpmap->numofshares = $addrcmpmap->numofshares;
                $oldaddrcmpmap->percentage = $addrcmpmap->percentage;
                $oldaddrcmpmap->save();
                $addrcmpmap->delete();
            }else {
                $addrcmpmap->active = 1;
                $addrcmpmap->approved = 1;
                $addrcmpmap->save();
            }
        }
        return redirect('shareholdercat');
    }

    public function getChunkArray($array){
       return array(
            'sdid' => $array[0],
            'id' => $array[1],
            'shareholdernum' => $array[2],
            'totalshares' => $array[3],
            'dematerializeform' => $array[4],
            'numofshares' => $array[5],
            'percentage' => $array[6],
        );
    }

    public function displayTotal(){
        $shareholderData = ShareholderCat::shareholderJoinIndex();
        $shareholderArray = $this->getRetrievedData($shareholderData);
        return view('shareholdercat.displaytotal')
            ->with('shareholderData', $shareholderData)
            ->with('shareArray', $shareholderArray);
    }

    public function shareholderPromoter($id){
        $individualShares = ShareMoreThanOnePercent::getIndividualShares();
        return view('shareholdercat.shareholderpromoter')
            ->with('allshares',$id)
            ->with('individualShares',$individualShares);
    }
    public function sharePromoter($id){
        $individualShares = ShareIndividualDetails::getIndividualShares();
        return view('shareholdercat.sharepromoter')
            ->with('allshares',$id)
            ->with('individualShares',$individualShares);

    }

    public function createIndividual(Request $request){
        $params = $request->all();
        $rules = array(
            'shareholdername' => 'required|string',
            'tnoshare' => 'required|numeric',
            'tgtotal' => 'required|numeric',
            'snoshare' => 'required|numeric',
//            'spercent' => 'required|numeric',
            'sgtotal' => 'required|numeric',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('shareholdercat/sharepromoter/'.$params['allshare'])
                ->withInput()
                ->withErrors($validator);
        }else{
            $shareholder = new ShareIndividualDetails();
            $shareholder->createIndividualEntry($params);
            return Redirect::to('shareholdercat/sharepromoter/'.$params['allshare']);
        }
    }

    public function createIndividualhanOne(Request $request){
        $params = $request->all();
        $rules = array(
            'shareholdername' => 'required|string',
            'noofshare' => 'required|numeric',
            'tpercent' => 'required|numeric',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('shareholdercat/shareholderpromoter/'.$params['allshare'])
                ->withInput()
                ->withErrors($validator);
        }else{
            if($params['tpercent']< 1){
                Session::flash('error', 'Record not saved. Number of shares must be grated than 1%');
                return Redirect::to('shareholdercat/shareholderpromoter/'.$params['allshare']);
            }else{
                $shareholder = new ShareMoreThanOnePercent();
                $shareholder->createIndividualEntry($params);
                return Redirect::to('shareholdercat/shareholderpromoter/'.$params['allshare']);
            }
        }
    }
}