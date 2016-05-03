<?php

namespace App\Http\Controllers\Masters;

use App\Helpers\HelperServiceProvider;
use App\Models\Company;
use App\Models\RelationView;
use App\Models\ShareholderCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ShareholderCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $company = HelperServiceProvider::getCompanyName();
        $cmpid = Session::get('cmpid');
        $shareholderArray = array();
        $shareholderData = ShareholderCategory::getDataFromShareholder();
//        $company = new Company();
//        $companyData =  $company->getAllCompanyDetail();
        $companyIds = ShareholderCategory::lists('companyid','id');
        foreach($shareholderData as $e){
            $shareholderArray[$e['id']] = array('rootid'=> $e['rootid'],'category' => $e['category'], 'codeId'=> $e['code'],'id' => $e['id']);
        }
       return view('shareholdercategory.index')
           ->with('shareholderData', $shareholderData)
           ->with('companyData', $company)
           ->with('cmpid', $cmpid)
           ->with('companyids', $companyIds)
           ->with('shareArray', $shareholderArray);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();
        if(!$params['id']){
            $sharesCategory = new ShareholderCategory();
            $sharesCategory->createShareholder($params);
        }else{
            $sharesCategory = new ShareholderCategory();
            $sharesCategory->createNewShareholder($params);
        }
        return redirect('shareholdercategory');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        return ShareholderCategory::find($id);
    }

    public function editCategory($id)
    {
        $company = HelperServiceProvider::getCompanyName();
        $cmpid = Session::get('cmpid');
        $pendingCategory = ShareholderCategory::find($id);
        $companyData = new Company();
//        $companyData = $companyData->getAllCompanyDetail();
        return view('shareholdercategory/edit')
            ->with('companyData', $company)
            ->with('cmpid', $cmpid)
            ->with('pendingCategory', $pendingCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $params = $request->all();
        if(!$id){
            $sharesCategory = new ShareholderCategory();
            $sharesCategory->createShareholder($params);
        }else{
            $shareholderData = ShareholderCategory::find($id);
                $shareholderData->companyid = $params['name'];
//                $shareholderData->packageid = $params['package'];
                $shareholderData->code = $params['code'];
                $shareholderData->rootid = $params['parents'];
                $shareholderData->category = $params['category'];
                $shareholderData->save();
        }
        return redirect('shareholdercategory');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getPendingCategory(){;
        $pendingCategory = ShareholderCategory::getPendingDataFromShareholder();
        return view('shareholdercategory/pending')
            ->with('pendingCategory', $pendingCategory);
    }

    public function postPendingCategory($id){
        $pendingCategory = ShareholderCategory::getDataFromShareholder();
        return view('shareholdercategory/pending')
            ->with('pendingCategory', $pendingCategory);
    }

    public function approvePendingCategory($id){
        $shareholderData = ShareholderCategory::find($id);
        $oid = $shareholderData->oid;
        if($oid > 0){
            $orignalData = ShareholderCategory::find($oid);
            $orignalData->companyid = $shareholderData['companyid'];
            $orignalData->packageid = $shareholderData['packageid'];
            $orignalData->code = $shareholderData['code'];
            $orignalData->rootid = $shareholderData['rootid'];
            $orignalData->category = $shareholderData['category'];
            $orignalData->active = 1;
            $orignalData->approved = 1;
            $orignalData->save();
            $shareholderData->delete();
        }else{
            $active = 1;
            $approve = 1;
            $temp = ShareholderCategory::findOrNew($id);
            $temp->active = $active;
            $temp->approved = $approve;
            $temp->save();
        }
        return redirect('shareholdercategory/pending');
    }

    public function rejectPendingCategory($id){
        $active = 1;
        $approve = 0;
        $temp = ShareholderCategory::findOrNew($id);
        $temp->active = $active;
        $temp->approved = $approve;
        $temp->save();
        return redirect('shareholdercategory/pending');
    }

    public function deleteCategory($id)
    {
        $table = new ShareholderCategory();
        $tname = $table->getTable();
        $status = RelationView::checkrelation($tname,$id);
        if($status){
            $refrence = ShareholderCategory::find($id);
            $refrence->active = 0;
            $refrence->save();
            Session::flash('success', 'Shareholder Category deleted Successfully !!');
        }else{
            Session::flash('error', 'Cannot perform delete operation. Related records found');
        }
        return redirect('shareholdercategory');
    }
}
