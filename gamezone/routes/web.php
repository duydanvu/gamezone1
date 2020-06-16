<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// route test api
Route::get('/testAPI','APIController@testCurl')->name('test_api');
Route::get('/testUserReg','APIController@userRegs')->name('test_user_reg');

//route login,logout
Route::get('/','Auth\CustomAuthController@showLoginForm')->name('login');
Route::post('/','Auth\CustomAuthController@login')->name('login_process');
Route::post('/logout','Auth\CustomAuthController@logout')->name('logout');

Route::group(['middleware' => ['web','checkLogOut']],function (){
    Route::get('/home', 'HomeController@index')->name('dashboard');

    Route::get('/test','ReportController@SearchDateTime')->name('test');

    //Admin
    Route::get('admin/list_use','SystemController@listUse')->name('admin_list_user');
    Route::get('admin/list_use/update/{id}','SystemController@updateUseView')->name('admin_list_update_user');
    Route::post('admin/list_use/update/post','SystemController@updateUseViewInfor')->name('admin_list_update_post_user');
    Route::post('admin/create/account','SystemController@registerProcess')->name('register_process');
    Route::get('admin/list_use/delete/{id}','SystemController@deleteUseView')->name('admin_list_delete_user');
    Route::get('admin/list_role/view','SystemController@viewRoleUse')->name('admin_list_role_use');
    Route::get('admin/list_role/update/{id}','SystemController@updateRoleView')->name('admin_list_update_role');
    Route::post('admin/list_role/update/post','SystemController@updateRoleViewInfor')->name('admin_role_update_post_user');
    Route::get('admin/list_role/add/{id}','SystemController@addRoleView')->name('admin_add_role');
    Route::post('admin/list_role/add/post','SystemController@addRoleViewInfor')->name('admin_role_add_new');
    Route::get('admin/list_role/delete_role/{id}/{role}','SystemController@deleteRoleFromAdmin')->name('admin_delete_role');

    //report
//    Route::get('report/report_day','ReportController@listReportDayAction')->name('report_day_action');
//    Route::get('report/report_bk','ReportController@listReportDayAction')->name('report_bk_action');
    Route::get('report/report_week','ReportController@listReportDayAction')->name('report_week_action');
//    Route::get('report/report_month','ReportController@listReportDayAction')->name('report_month_action');
//    Route::get('report/report_year','ReportController@listReportDayAction')->name('report_year_action');


    Route::post('report/search/loadData/','ReportController@SearchDateTime')->name('search_date_time');

    //Customer service
    Route::get('cussv/reg_tran','CustomerServiceController@regTransactions')->name('cus_sv_reg');
    Route::post('cussv/reg_tran/loadData/','CustomerServiceController@SearchDateTimeRegTran')->name('search_date_time_regtran');

    Route::get('cussv/unreg_tran','CustomerServiceController@unregTransactions')->name('cus_sv_unreg');
    Route::post('cussv/unreg_tran/loadData/','CustomerServiceController@SearchDateTimeUnRegTran')->name('search_date_time_unreg_tran');

    Route::get('cussv/momt','CustomerServiceController@moMt')->name('cus_sv_momt');
    Route::post('cussv/momt/loadData/','CustomerServiceController@SearchDateTimeMOMT')->name('search_date_time_momt');

    Route::get('cussv/his_acc','CustomerServiceController@historyAccount')->name('cus_sv_history');
    Route::post('cussv/his_acc/loadData/','CustomerServiceController@SearchDateTimeHisAcc')->name('search_date_time_his');

    Route::get('cussv/his_acc_use','CustomerServiceController@historyAccountUse')->name('cus_sv_history_use');
    Route::post('cussv/his_acc_use/loadData/','CustomerServiceController@SearchDateTimeHisAccUse')->name('search_date_time_his_use');

    Route::get('cussv/exten_acc','CustomerServiceController@extenAcc')->name('cus_sv_exten_acc');
    Route::post('cussv/exten_acc/loadData/','CustomerServiceController@SearchDateTimeExtenAcc')->name('search_date_time_exten_acc');

    Route::get('cussv/history_log','CustomerServiceController@HistoryLog')->name('cus_sv_history_log');

    Route::get('cussv/upload_sub','CustomerServiceController@UploadFileSub')->name('cus_sv_upload_sub');
    Route::post('cussv/upload_sub/po','CustomerServiceController@DoUploadToSub')->name('cus_sv_upload_sub_post');
    Route::post('cussv/upload_unsub/po','CustomerServiceController@DoUploadToUnSub')->name('cus_sv_upload_unsub_post');

    Route::get('cussv/sub_unsub_acc','CustomerServiceController@subUnSubAcc')->name('cus_sv_sub_unsub_acc');
    Route::get('cussv/information_acc','CustomerServiceController@subUnSubAcc')->name('cus_sv_information_acc');
    Route::get('cussv/sub_unsub/{id}/{epiTime}','CustomerServiceController@subUnSubViewUpdate')->name('sub_unsub_update');
    Route::post('cussv/sub_unsub/','CustomerServiceController@subUnSubUpdateRequest')->name('update_account_in_list');

    //KPI
    Route::get('kpi/infor','KPIController@listKPIAction')->name('kpi_information');

});
