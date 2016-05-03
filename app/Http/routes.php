<?php
/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
| 
*/

Route::get('/', function () {
    return view('auth/login');
});



Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::group(['middleware' => 'web'], function () {
    //home-dashboard
    Route::get('companysession/cmpsearchajax', 'HomeController@cmpSearchAjax');
    Route::get('companysession/{id}', 'HomeController@companysession');
    Route::get('/', 'HomeController@index');
    Route::get('dashboard', 'HomeController@dashboard');
    // Authentication routes...
    Route::get('auth/login', 'Auth\AuthController@getLogin');
    Route::post('auth/login', 'Auth\AuthController@postLogin');
    Route::get('auth/logout', 'Auth\AuthController@getLogout');
    // Registration routes...
    Route::get('auth/register', 'Auth\AuthController@getRegister');
    Route::post('auth/register', 'Auth\AuthController@postRegister');
    Route::auth();

    Route::resource('relationview', 'RelationViewController');
    //Developed By Rahul Tathod Routes Masters......//
    Route::get('accountcategory/pending', 'Masters\AccountCategoryController@getPendingAccCategory');
    Route::get('accountcategory/{id}/edit/{pendview}', 'Masters\AccountCategoryController@edit');
    Route::get('accountcategory/reject/{id}', 'Masters\AccountCategoryController@reject');
    Route::get('accountcategory/pending/{id}', 'Masters\AccountCategoryController@postPendingAccCategory');
    Route::resource('accountcategory', 'Masters\AccountCategoryController');

    Route::get('assetclassification/pending', 'Masters\AssetClassificationController@getPendingAssClass');
    Route::get('assetclassification/{id}/edit/{pendview}', 'Masters\AssetClassificationController@edit');
    Route::get('assetclassification/reject/{id}', 'Masters\AssetClassificationController@reject');
    Route::get('assetclassification/pending/{id}', 'Masters\AssetClassificationController@postPendingAssClass');
    Route::resource('assetclassification', 'Masters\AssetClassificationController');

    Route::get('borrowerclass/pending', 'Masters\BorrowerClassController@getPendingBorrowerClass');
    Route::get('borrowerclass/{id}/edit/{pendview}', 'Masters\BorrowerClassController@edit');
    Route::get('borrowerclass/reject/{id}', 'Masters\BorrowerClassController@reject');
    Route::get('borrowerclass/pending/{id}', 'Masters\BorrowerClassController@postPendingBorrowerClass');
    Route::resource('borrowerclass', 'Masters\BorrowerClassController');

    Route::get('city/statedetail', 'Masters\CityController@getStateDetail');
    Route::get('city/pending', 'Masters\CityController@getPendingCity');
    Route::get('city/{id}/edit/{pendview}','Masters\CityController@edit');
    Route::get('city/reject/{id}', 'Masters\CityController@reject');
    Route::get('city/pending/{id}', 'Masters\CityController@postPendingCity');
    Route::resource('city', 'Masters\CityController');

    Route::get('companytype/pending', 'Masters\CompanyTypeController@getPendingCompanyType');
    Route::get('companytype/{id}/edit/{pendview}','Masters\CompanyTypeController@edit');
    Route::get('companytype/reject/{id}', 'Masters\CompanyTypeController@reject');
    Route::get('companytype/pending/{id}', 'Masters\CompanyTypeController@postPendingCompanyType');
    Route::resource('companytype', 'Masters\CompanyTypeController');

    Route::get('country/pending', 'Masters\CountryController@getPendingCountry');
    Route::get('country/{id}/edit/{pendview}','Masters\CountryController@edit');
    Route::get('country/reject/{id}', 'Masters\CountryController@reject');
    Route::get('country/pending/{id}', 'Masters\CountryController@postPendingCountry');
    Route::resource('country', 'Masters\CountryController');

    Route::get('designation/pending', 'Masters\DesignationController@getPendingDesignation');
    Route::get('designation/{id}/edit/{pendview}', 'Masters\DesignationController@edit');
    Route::get('designation/reject/{id}', 'Masters\DesignationController@reject');
    Route::get('designation/pending/{id}', 'Masters\DesignationController@postPendingDesignation');
    Route::resource('designation', 'Masters\DesignationController');

    Route::get('exitreason/pending', 'Masters\ExitReasonController@getPendingExitReason');
    Route::get('exitreason/{id}/edit/{pendview}', 'Masters\ExitReasonController@edit');
    Route::get('exitreason/reject/{id}', 'Masters\ExitReasonController@reject');
    Route::get('exitreason/pending/{id}', 'Masters\ExitReasonController@postPendingExitReason');
    Route::resource('exitreason', 'Masters\ExitReasonController');

    Route::resource('exit', 'ExitdocController');

    Route::get('facilities/pending', 'Masters\FacilityController@getPendingFacility');
    Route::get('facilities/reject/{id}', 'Masters\FacilityController@rejectFacility');
    Route::get('facilities/{id}/edit/{pendview}', 'Masters\FacilityController@edit');
    Route::get('facilities/pending/{id}', 'Masters\FacilityController@postPendingFacility');
    Route::resource('facilities', 'Masters\FacilityController');

    Route::get('finparameter/pending', 'Masters\FinParameterController@getPendingFinParameter');
    Route::get('finparameter/reject/{id}', 'Masters\FinParameterController@rejectFacility');
    Route::get('finparameter/{id}/edit/{pendview}', 'Masters\FinParameterController@edit');
    Route::get('finparameter/pending/{id}', 'Masters\FinParameterController@postPendingFinParameter');
    Route::get('finparameter/getfindescription', 'Masters\FinParameterController@getFinDescription');
    Route::resource('finparameter', 'Masters\FinParameterController');

    Route::get('finparameterconfig/pending', 'Masters\FinParameterConfigController@getPendingFinParameterConfig');
    Route::get('finparameterconfig/reject/{id}', 'Masters\FinParameterConfigController@rejectFacility');
    Route::get('finparameterconfig/{id}/edit/{pendview}', 'Masters\FinParameterConfigController@edit');
    Route::get('finparameterconfig/pending/{id}', 'Masters\FinParameterConfigController@postPendingFinParameterConfig');
    Route::resource('finparameterconfig', 'Masters\FinParameterConfigController');

    Route::get('fyyear/pending', 'Masters\FyYearController@getPendingFyYears');
    Route::get('fyyear/reject/{id}', 'Masters\FyYearController@rejectFacility');
    Route::get('fyyear/{id}/edit/{pendview}', 'Masters\FyYearController@edit');
    Route::get('fyyear/pending/{id}', 'Masters\FyYearController@postPendingFyYears');
    Route::resource('fyyear', 'Masters\FyYearController');

    Route::get('groups/pending', 'Masters\GroupController@getPendingGroup');
    Route::get('groups/reject/{id}', 'Masters\GroupController@rejectFacility');
    Route::get('groups/{id}/edit/{pendview}', 'Masters\GroupController@edit');
    Route::get('groups/pending/{id}', 'Masters\GroupController@postPendingGroup');
    Route::resource('groups', 'Masters\GroupController');

    Route::get('holdingstatus/pending', 'Masters\HoldingStatusController@getPendingHoldingStatus');
    Route::get('holdingstatus/reject/{id}', 'Masters\HoldingStatusController@reject');
    Route::get('holdingstatus/{id}/edit/{pendview}', 'Masters\HoldingStatusController@edit');
    Route::get('holdingstatus/pending/{id}', 'Masters\HoldingStatusController@postPendingHoldingStatus');
    Route::resource('holdingstatus', 'Masters\HoldingStatusController');

    Route::get('institute/pending', 'Masters\InstituteController@getPendingInstitute');
    Route::get('institute/reject/{id}', 'Masters\InstituteController@rejectFacility');
    Route::get('institute/{id}/edit/{pendview}', 'Masters\InstituteController@edit');
    Route::get('institute/pending/{id}', 'Masters\InstituteController@postPendingInstitute');
    Route::resource('institute', 'Masters\InstituteController');

    Route::get('industry/pending', 'Masters\IndustryController@getPendingIndustry');
    Route::get('industry/reject/{id}', 'Masters\IndustryController@rejectFacility');
    Route::get('industry/{id}/edit/{pendview}', 'Masters\IndustryController@edit');
    Route::get('industry/pending/{id}', 'Masters\IndustryController@postPendingIndustry');
    Route::resource('industry', 'Masters\IndustryController');

    Route::get('iracstatus/pending', 'Masters\IracStatusController@getPendingIracStatus');
    Route::get('iracstatus/reject/{id}', 'Masters\IracStatusController@rejectFacility');
    Route::get('iracstatus/{id}/edit/{pendview}', 'Masters\IracStatusController@edit');
    Route::get('iracstatus/pending/{id}', 'Masters\IracStatusController@postPendingIracStatus');
    Route::resource('iracstatus', 'Masters\IracStatusController');

    Route::get('lenderaggrstatus/pending', 'Masters\LenderaggrstatusController@getPendingLenderAgrrStatus');
    Route::get('lenderaggrstatus/reject/{id}', 'Masters\LenderaggrstatusController@rejectFacility');
    Route::get('lenderaggrstatus/{id}/edit/{pendview}', 'Masters\LenderaggrstatusController@edit');
    Route::get('lenderaggrstatus/pending/{id}', 'Masters\LenderaggrstatusController@postPendingLenderAgrrStatus');
    Route::resource('lenderaggrstatus', 'Masters\LenderaggrstatusController');

    Route::get('lenders/pending', 'Masters\LenderController@getPendingLender');
    Route::get('lenders/reject/{id}', 'Masters\LenderController@rejectFacility');
    Route::get('lenders/{id}/edit/{pendview}', 'Masters\LenderController@edit');
    Route::get('lenders/pending/{id}', 'Masters\LenderController@postPendingLender');
    Route::resource('lenders', 'Masters\LenderController');

    Route::get('lendertypes/pending', 'Masters\LenderTypeController@getPendingLenderType');
    Route::get('lendertypes/reject/{id}', 'Masters\LenderTypeController@reject');
    Route::get('lendertypes/{id}/edit/{pendview}', 'Masters\LenderTypeController@edit');
    Route::get('lendertypes/pending/{id}', 'Masters\LenderTypeController@postPendingLenderType');
    Route::resource('lendertypes', 'Masters\LenderTypeController');

    Route::get('mcmember/pending', 'Masters\McMemberController@getPendingMcMember');
    Route::get('mcmember/reject/{id}', 'Masters\McMemberController@reject');
    Route::get('mcmember/{id}/edit/{pendview}', 'Masters\McMemberController@edit');
    Route::get('mcmember/pending/{id}', 'Masters\McMemberController@postPendingMcMember');
    Route::resource('mcmember', 'Masters\McMemberController');

    Route::get('package/pending', 'Masters\PackageController@getPendingPackage');
    Route::get('package/reject/{id}', 'Masters\PackageController@reject');
    Route::get('package/{id}/edit/{pendview}', 'Masters\PackageController@edit');
    Route::get('package/pending/{id}', 'Masters\PackageController@postPendingPackage');
    Route::resource('package', 'Masters\PackageController');

    Route::get('performanceparameter/pending', 'Masters\PerformanceParameterController@getPendingperformanceParameter');
    Route::get('performanceparameter/reject/{id}', 'Masters\PerformanceParameterController@reject');
    Route::get('performanceparameter/{id}/edit/{pendview}', 'Masters\PerformanceParameterController@edit');
    Route::get('performanceparameter/pending/{id}', 'Masters\PerformanceParameterController@postPendingperformanceParameter');
    Route::resource('performanceparameter', 'Masters\PerformanceParameterController');

    Route::get('promoterdetails/pending', 'PromoterdetailController@getPendingPromoter');
    Route::get('promoterdetails/reject/{id}', 'PromoterdetailController@reject');
    Route::get('promoterdetails/{id}/edit/{pendview}', 'PromoterdetailController@edit');
    Route::get('promoterdetails/pending/{id}', 'PromoterdetailController@postPendingPromoter');
    Route::resource('promoterdetails', 'PromoterdetailController');

    Route::get('sectors/pending', 'Masters\SectorController@getPendingSector');
    Route::get('sectors/reject/{id}', 'Masters\SectorController@reject');
    Route::get('sectors/{id}/edit/{pendview}', 'Masters\SectorController@edit');
    Route::get('sectors/pending/{id}', 'Masters\SectorController@postPendingSector');
    Route::resource('sectors', 'Masters\SectorController');

    Route::get('securityrate/pending', 'Masters\SecurityRateController@getPendingsecurityrates');
    Route::get('securityrate/reject/{id}', 'Masters\SecurityRateController@reject');
    Route::get('securityrate/{id}/edit/{pendview}', 'Masters\SecurityRateController@edit');
    Route::get('securityrate/pending/{id}', 'Masters\SecurityRateController@postPendingsecurityrates');
    Route::resource('securityrate', 'Masters\SecurityRateController');

    Route::get('exposure/pending', 'Masters\ExposureController@getPending');
    Route::get('exposure/reject/{id}', 'Masters\ExposureController@reject');
    Route::get('exposure/{id}/edit/{pendview}', 'Masters\ExposureController@edit');
    Route::get('exposure/pending/{id}', 'Masters\ExposureController@postPending');
    Route::resource('exposure', 'Masters\ExposureController');


    Route::get('state/pending', 'Masters\StateController@getPendingState');
    Route::get('state/reject/{id}', 'Masters\StateController@reject');
    Route::get('state/{id}/edit/{pendview}', 'Masters\StateController@edit');
    Route::get('state/pending/{id}', 'Masters\StateController@postPendingState');
    Route::resource('state', 'Masters\StateController');

    Route::get('test', 'Masters\CityController@test');
    Route::get('utility/listdoc', 'Utility\UtilityController@listdoc');
    Route::post('utility/upload', 'Utility\UtilityController@upload');
    Route::get('utility/updatedoc', 'Utility\UtilityController@updatedoc');
    Route::resource('utility', 'Utility\UtilityController');

//    Route::get('company/addrpending', 'Company\CompanyController@postPendingAddr');

    Route::get('company/rejectpendaddr/{addrid}', 'Company\CompanyController@rejectPendingAddr');
    Route::get('company/approvependaddr/{addrid}', 'Company\CompanyController@approvePendingAddr');
    Route::post('company/pendingaddr', 'Company\CompanyController@postPendingAddr');
    Route::get('company/editpendingaddr/{addrid}/{pendview}', 'Company\CompanyController@editPendingAddr');
    Route::get('company/pendingaddr', 'Company\CompanyController@getPendingAddr');
    Route::get('company/pending', 'Company\CompanyController@getPendingCompany');
    Route::get('company/reject/{id}', 'Company\CompanyController@rejectPendingCompany');
    Route::post('company/pending/{id}', 'Company\CompanyController@postPendingCompany');
    Route::get('company/pending/{id}', 'Company\CompanyController@postPendingCompany');
    Route::get('company/update/{cmpid}/{isedit}', 'Company\CompanyController@update');
    Route::get('company/update/{cmpid}', 'Company\CompanyController@update');
    Route::get('company/showtoapprove/{cmpid}', 'Company\CompanyController@viewPendingToApprove');
    Route::get('company/cityDetail', 'Company\CompanyController@cityDetails');
    Route::get('company/countryDetail', 'Company\CompanyController@stateDetails');
//    Route::get('company/editcmpaddr/{addrid}', 'Company\CompanyController@getCompanyAddress');
    Route::get('company/companyaddress/{isedit}', 'Company\CompanyController@getCompanyAddress');
    Route::post('company/companyaddress', 'Company\CompanyController@postCompanyAddress');
    Route::resource('company', 'Company\CompanyController');
    //Developed By Rahul Tathod Routes Working Screens......//////////////////////////////////////////////////
    Route::get('finperformance/pending', 'FinPerformanceController@getPendingperformanceParameter');
    Route::post('finperformance/pending/{id}', 'FinPerformanceController@postPendingperformanceParameter');
    Route::get('finperformance/getfinparameter', 'FinPerformanceController@getfinparameter');
    Route::resource('finperformance', 'FinPerformanceController');


    Route::get('companylendermap/cdr', 'CompanyLenderMap\CompanyLenderMapController@getCdrData');
    Route::get('companylendermap/noncdr', 'CompanyLenderMap\CompanyLenderMapController@getNonCdrData');
    Route::get('companylendermap/transaction', 'CompanyLenderMap\CompanyLenderMapController@getTransactionData');
    Route::resource('companylendermap', 'CompanyLenderMap\CompanyLenderMapController');

    Route::post('implementation/implemented', 'ImplementationController@implemented');
    Route::get('implementation/mra', 'ImplementationController@getmra');
    Route::post('implementation/mra', 'ImplementationController@postmra');
    Route::get('implementation/restructure', 'ImplementationController@getmra1');
    Route::post('implementation/restructure', 'ImplementationController@postmra1');
    Route::resource('implementation', 'ImplementationController');

    Route::post('monitoring/updatemonitoring', 'MonitoringController@updatemonitoring');
    Route::get('monitoring/{isedit}', 'MonitoringController@index');
    Route::resource('monitoring', 'MonitoringController');

    Route::get('pocontacts/pending', 'PocontactController@getPendingPocontact');
    Route::get('pocontacts/reject/{id}', 'PocontactController@reject');
    Route::get('pocontacts/{id}/edit/{pendview}', 'PocontactController@edit');
    Route::get('pocontacts/pending/{id}', 'PocontactController@postPendingPocontact');
    Route::resource('pocontacts', 'PocontactController');

    Route::resource('meeting', 'MeetingController');

    Route::resource('postrestructuring', 'PostRestructuringController');


    Route::get('shareholdercategory/pending', 'Masters\ShareholderCategoryController@getPendingCategory');
    Route::get('shareholdercategory/pending/{id}', 'Masters\ShareholderCategoryController@approveCategory');
    Route::post('shareholdercategory/pending/{id}', 'Masters\ShareholderCategoryController@postPendingCategory');
    Route::get('shareholdercategory/editcategory/{id}', 'Masters\ShareholderCategoryController@editCategory');
    Route::get('shareholdercategory/destroy/{id}', 'Masters\ShareholderCategoryController@deleteCategory');
    Route::get('shareholdercategory/approvependingcategory/{id}', 'Masters\ShareholderCategoryController@approvePendingCategory');
    Route::get('shareholdercategory/rejectpendingcategory/{id}', 'Masters\ShareholderCategoryController@rejectPendingCategory');
    Route::resource('shareholdercategory','Masters\ShareholderCategoryController');


    Route::get('lenderdetails/pending', 'LenderDetailController@getPendingLender');
    Route::get('lenderdetails/reject/{id}', 'LenderDetailController@rejectFacility');
    Route::get('lenderdetails/{id}/edit/{pendview}', 'LenderDetailController@edit');
    Route::get('lenderdetails/pending/{id}', 'LenderDetailController@postPendingLender');
    Route::get('lenderdetails/getlender', 'LenderDetailController@getlender');
    Route::resource('lenderdetails', 'LenderDetailController');

    Route::get('facilitydetails/getfacility', 'FacilitydetailController@getfacility');
    Route::resource('facilitydetails', 'FacilitydetailController');

    Route::post('finparameterdetails/savefinratio', 'FinParameterdetailController@saveFinRatio');
    Route::resource('finparameterdetails', 'FinParameterdetailController');

    Route::get('institutedetails/pending', 'InstitutedetailController@getPendingInstitute');
    Route::post('institutedetails/pending/{id}', 'InstitutedetailController@postPendingInstitute');
    Route::get('institutedetails/cdr', 'InstitutedetailController@getCdrData');
    Route::get('institutedetails/noncdr', 'InstitutedetailController@getNonCdrData');
    Route::get('institutedetails/{isedit}', 'InstitutedetailController@index');
    Route::resource('institutedetails', 'InstitutedetailController');

    Route::resource('timelinemaster', 'Masters\TimelineMasterController');

    Route::get('timelinetemplate/t2', 'Masters\TimelineTemplateController@indext2');
    Route::post('timelinetemplate/update', 'Masters\TimelineTemplateController@update');
    Route::resource('timelinetemplate', 'Masters\TimelineTemplateController');

    Route::get('shareholdercat/shareholderpromoter/{id}', 'ShareholderCatController@shareholderPromoter');
    Route::post('shareholdercat/createindividual', 'ShareholderCatController@createIndividual');
    Route::post('shareholdercat/createindividualmorethanone', 'ShareholderCatController@createIndividualhanOne');
    Route::get('shareholdercat/sharepromoter/{id}', 'ShareholderCatController@sharePromoter');
    Route::post('shareholdercat/updateDetails', 'ShareholderCatController@updateDetails');
    Route::get('shareholdercat/pending', 'ShareholderCatController@approveShareholderDetails');
    Route::get('shareholdercat/display', 'ShareholderCatController@displayTotal');
    Route::get('shareholdercat/reject', 'ShareholderCatController@reject');
    Route::get('shareholdercat/update', 'ShareholderCatController@update');
    Route::post('shareholdercat/rejectpending', 'ShareholderCatController@rejectPending');
    Route::post('shareholdercat/updatepending', 'ShareholderCatController@updatePending');
    Route::post('shareholdercat/approve', 'ShareholderCatController@approve');
    Route::get('shareholdercat/createShareholder', 'ShareholderCatController@createShareholder');
    Route::get('shareholdercat/updateShareholderDetails', 'ShareholderCatController@updateShareholderDetails');
    Route::resource('shareholdercat', 'ShareholderCatController');

    Route::get('timeline/deletetimeline/{tdetailid}', 'TimelineController@deleteTimeline');
    Route::get('timeline/timelinedetails', 'TimelineController@timelineDetails');
    Route::get('timeline/deletedoc/{cmpdocid}', 'TimelineController@deleteDoc');

    Route::get('timeline/gethighexpobysysrev', 'Masters\TimelineTemplateController@getHighexpoBySysrev');
    Route::get('timeline/getsysrevajax', 'TimelineController@getSysrevAjax');
    Route::post('timeline/{id}/update', 'TimelineController@update');
    Route::post('timeline/jlflcreate', 'TimelineController@jlflCreate');
    Route::post('timeline/jlfhcreate', 'TimelineController@jlfhCreate');

    Route::post('timeline/jlfh', 'TimelineController@jlfh');
    Route::post('timeline/jlfl', 'TimelineController@jlfl');
    Route::post('timeline/flash', 'TimelineController@flash');
    Route::get('timeline/docsbystageid', 'TimelineController@docsByStageId');
    Route::resource('timeline', 'TimelineController');
    Route::get('exposuredetails/compare/{id}', 'ExposureDetailsController@compareExposure');
    Route::post('exposuredetails/compare', 'ExposureDetailsController@compareExposure');
    Route::post('exposuredetails/showsingleexposure', 'ExposureDetailsController@index');
    Route::get('exposuredetails/showsingleexposure/{id}', 'ExposureDetailsController@singleExposure');
    Route::get('exposuredetails/contribution/{id}', 'ExposureDetailsController@contibuteExposure');
    Route::get('exposuredetails/getlenders', 'ExposureDetailsController@getLenders');
    Route::post('exposuredetails/savecontribution', 'ExposureDetailsController@saveContribution');
    Route::get('exposuredetails/update/{id}', 'ExposureDetailsController@updateExposure');
    Route::get('exposuredetails/newindex', 'ExposureDetailsController@newExposure');
    Route::post('exposuredetails/saveallexposure', 'ExposureDetailsController@saveAllExposure');
    Route::post('exposuredetails/updateexposuredetails', 'ExposureDetailsController@updateExposureDetails');
    Route::resource('exposuredetails', 'ExposureDetailsController');

    Route::get('user/reject/{id}', 'Masters\UserController@reject');
    Route::get('user/profile', 'Masters\UserController@profile');
    Route::get('user/pending', 'Masters\UserController@getPendingUser');
    Route::get('user/pending/{id}', 'Masters\UserController@postPendingUser');
    Route::resource('user', 'Masters\UserController');

    Route::get('report1', 'ReportController@report_1');
    Route::get('report2Index', 'ReportController@report2Index');
    Route::post('report2', 'ReportController@report_2');
    Route::get('report3', 'ReportController@report_3');
    Route::get('report4', 'ReportController@report_4');
    Route::get('report5', 'ReportController@report_5');
    Route::get('report6', 'ReportController@report_6');
    Route::get('report7', 'ReportController@report_7');
    Route::get('report8a', 'ReportController@report_8a');
    Route::get('report8b', 'ReportController@report_8b');
    Route::resource('report', 'ReportController');

});

Route::get('email', function () { $data = array(
        'name' => "Laravel CDR", );
    Mail::send('emails.mail', $data, function ($message) {
        $message->from('napster@gmail.com', 'Laravel CDR');
        $message->to('vinodpp22@gmail.com')->subject('Learning Laravel test email');
        $message->replyTo('noreply@gmail.com');
//        $message->attachData($data, $name, array $options = []);
    });

    return "Your email has been sent successfully";

});



