<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\AttachFile;
use App\Http\Controllers\Auth\RegisterController;
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
//Verify Email
Route::get('/hello', function () {
    return 'Hello World';
});
Route::get('activated-mail/{code}/{redirect_uri?}', 'Auth\RegisterController@ActivatedMail');

Route::get('funtions/set-cookie', 'Funtion\\FuntionsController@setCookie');
Route::get('funtions/get-cookie', 'Funtion\\FuntionsController@getCookie');
Route::get('funtions/get-branch-data/{id?}', 'Funtion\\FuntionsController@GetBranchData');
Route::get('funtions/auto-refresh/notification', 'Funtion\\FuntionsController@getNotification');
Route::get('funtions/redirect/notification/{id?}', 'Funtion\\FuntionsController@Notification_redirect');
Route::Post('funtions/read_all/notification', 'Funtion\\FuntionsController@NotificationReadAll');
Route::get('funtions/clear-log', 'Funtion\\FuntionsController@ClearLog');

Route::get('/funtions/treeview_scope', 'Funtion\\FuntionsController@GetDataTestItem');

//IBCB
Route::get('section5/ibcb_list', 'Funtion\\Section5Controller@ibcb_list');
Route::get('section5/ibcb_by_scope', 'Funtion\\Section5Controller@ibcb_by_scope');
Route::get('section5/ibcb/{code?}', 'Funtion\\Section5Controller@ibcb_detail');
Route::get('section5/data_ibcb_scope_list', 'Funtion\\Section5Controller@data_ibcb_scope_list');
Route::get('section5/data_ibcb_list', 'Funtion\\Section5Controller@data_ibcb_list');
Route::get('section5/data_ibcbs_list', 'Funtion\\Section5Controller@data_ibcbs_list');
Route::get('welcome/section5/ibcb_list', 'Funtion\\Section5Controller@welcome_ibcb_list');
Route::get('welcome/section5/ibcb_by_scope', 'Funtion\\Section5Controller@welcome_ibcb_by_scope');

//LAB
Route::get('section5/labs_list', 'Funtion\\Section5Controller@labs_list');
Route::get('section5/data_labs_list', 'Funtion\\Section5Controller@data_labs_list');
Route::get('request-section-5/labs/show/{id}', 'Section5\\ApplicationLabController@labs_show');
Route::get('welcome/section5/labs_list', 'Funtion\\Section5Controller@welcome_labs_list');

Route::get('section5/get-branch-data/{id?}', 'Funtion\\Section5Controller@GetBranchData');
Route::get('section5/get-test-item/{tis_id?}', 'Funtion\\Section5Controller@GetTestItem');

Route::get('funtions/search-standards', 'Funtion\\FuntionsController@SearchStandards');
Route::get('funtions/search-addreess', 'Funtion\\FuntionsController@SearchAddreess');
Route::get('funtions/get-addreess/{subdistrict_id?}', 'Funtion\\FuntionsController@GetAddreess');
Route::get('funtions/get-section5-lab/{lab_id?}', 'Funtion\\FuntionsController@GetSection5Lab');
Route::get('funtions/get-section5-lab-scope/{lab_id?}', 'Funtion\\FuntionsController@GetSection5LabScope');

Route::get('funtions/get-section5-ibcb-list', 'Funtion\\FuntionsController@DataOptionIBCB');
Route::get('funtions/get-section5-ibcb/{ibcb_id?}', 'Funtion\\FuntionsController@GetSection5IBCB');



Route::get('funtions/get-view/files/{systems}/{tax_number}/{new_filename}/{filename}', function ($systems, $tax_number, $new_filename, $filename) {
    $public = public_path();
    $attach_path = 'files/' . $systems . '/' . $tax_number;

    if (HP::checkFileStorage($attach_path . '/' . $new_filename)) {

        $file_name = $attach_path . '/' . $new_filename;
        $info = pathinfo($file_name, PATHINFO_EXTENSION);

        if ($info == "txt" || $info == "doc" || $info == "docx" || $info == "ppt" || $info == "7z" || $info == "zip") {
            return Storage::download($attach_path . '/' . $new_filename);
        } else {
            HP::getFileStorage($attach_path . '/' . $new_filename);
            $filePath = response()->file($public . '/uploads/' . $attach_path . '/' . $new_filename);
            return $filePath;
        }
    } else {
        return 'ไม่พบไฟล์';
    }
});


Route::get('funtions/get-delete/files/{id?}/{url_send?}', function ($id, $url_send) {

    if (!is_null($id) && is_numeric($id) && !is_null(AttachFile::where('id', $id)->first())) {

        $file = AttachFile::where('id', $id)->first();

        if (HP::checkFileStorage($file->url)) {
            Storage::delete("/" . $file->url);
        }
        $file->delete();

        return redirect(base64_decode($url_send))->with('delete_message', 'Delete Complete!');
    }

});

//โชว์ค่า
Route::get('/app-show-config', function () {
    return Config::get('session.lifetime');
});

Route::get('/welcome', function () {
    return view('welcome.index');
});

Route::get('/', function () {
    return view('dashboard.dashboard');
})->middleware('auth');

Route::get('/home', function () {
    return view('home');
})->middleware('auth');

Route::get('test-prefix', function () {
    return App\Models\Basic\Prefix::pluck('title', 'id');
});

//ลงทะเบียน

use Illuminate\Http\Request;

// i-Industry SSO Callback + Register (no auth)

use App\Http\Controllers\Auth\IIndustryCallbackController;
use App\Http\Controllers\Auth\ThaiDCallbackController;

Route::match(['GET', 'POST'], '/internal/iindustry-callback', [IIndustryCallbackController::class, 'handle'])
    ->name('iindustry.callback');

// ThaiD SSO Callback - เรียกเมื่อ browser มี cookie "sessionThaID" (domain .tisi.go.th) อยู่แล้ว
// ยังไม่ยืนยัน entry point จริง (LoginThaID2.asp ต้องรู้จัก redirect กลับมาที่ route นี้ - ดู docs)
Route::match(['GET', 'POST'], '/internal/thaid-callback', [ThaiDCallbackController::class, 'handle'])
    ->name('thaid.callback');

Route::get('/register/fill', [RegisterController::class, 'showFill'])->name('register.fill');

Auth::routes(['register' => false]);
/*Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])
    ->middleware(['web','guest'])
     ->name('register');
*/
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

//Kantapon 29/9/2568 For Test Only
/*
Route::get('/test/iindustry', function (Request $r) {
    // allow overriding via query, defaults are just for quick tests
    $uid = (string) $r->query('uid', '1309902656213');
    $aid = (string) $r->query('aid', '30');
    $val = $uid . '/' . $aid;

    // set the same cookie your callback expects
    // 10 minutes, SameSite=Lax, not Secure (for http:// local)
    $cookie = cookie('i%2Dindustry', $val, 10, '/', null, false, false, false, 'Lax');
    Log::debug('This is cookie.', ['cookie' => $cookie]);

    // then send the browser to the normal callback (same host!)
    return redirect('/internal/iindustry-callback')->withCookie($cookie);
});*/


//fallback
/*
Route::post('/prereg/seed', function (\Illuminate\Http\Request $request) {
    // ป้องกันใช้ผิดที่: เปิดเฉพาะ local/dev หรือมี TEST_KEY ถูกต้อง
    abort_unless(app()->environment('local') || $request->header('X-TEST-KEY') === env('PREREG_TEST_KEY'), 403);

    $jt  = $request->input('jt');       // '1'|'2'|'3'
    $bid = $request->input('bid');      // เลขนิติ (ถ้ามี)
    $uid = $request->input('uid');      // เลขบัตร (ถ้ามี)

    $token = \Str::random(24);
    $snapshot = [
        'source' => 'i-industry',
        'jt'     => $jt,
        'bid'    => $bid,
        'uid'    => $uid,
    ];
    \Cache::put('prereg:'.$token, $snapshot, now()->addMinutes(30));
    session(['__prereg_token' => $token]);   // เก็บไว้ใน session เพื่อไม่ต้องพก token ใน URL

    return response()->json(['ok' => true, 'token' => $token]);
})->middleware('web')->name('prereg.seed');
*/

Route::post('/prereg/seed', function (\Illuminate\Http\Request $request) {
    abort_unless(app()->environment('local') || $request->header('X-TEST-KEY') === env('PREREG_TEST_KEY'), 403);

    $token = \Str::random(24);
    $snapshot = [
        'source' => 'i-industry',
        'jt' => (string) $request->input('jt'),
        'bid' => (string) $request->input('bid'),
        'uid' => (string) $request->input('uid'),
    ];

    // ✅ SESSION (not Cache)
    $request->session()->put('prereg:' . $token, $snapshot);
    $request->session()->save();

    return response()->json(['ok' => true, 'token' => $token]);
})->middleware('web')->name('prereg.seed');



Route::POST('auth/register/datatype', 'Auth\RegisterController@datatype');
Route::POST('auth/register/get_house_address', 'Auth\RegisterController@get_house_address');
Route::POST('auth/register/check_tax_number', 'Auth\RegisterController@check_tax_number');
Route::POST('auth/register/get_tax_number', 'Auth\RegisterController@get_tax_number');
Route::POST('auth/register/get_legal_entity', 'Auth\RegisterController@get_legal_entity');
Route::POST('auth/register/get_legal_faculty', 'Auth\RegisterController@get_legal_faculty');
Route::POST('auth/register/get_taxid', 'Auth\RegisterController@get_taxid');
Route::POST('auth/register/check_email', 'Auth\RegisterController@check_email');

//ลงทะเบียนสาขา
Route::get('register-branch', 'Auth\RegisterBranchController@index');
Route::POST('register-branch', 'Auth\RegisterBranchController@register');

Route::POST('auth/reset-email/inform', 'Auth\ResetEmailController@inform')->name('reset-email.inform');

//ลืมชื่อผู้ใช้งาน
Route::get('forgot-user', 'Auth\ForgotUserController@index');
Route::POST('forgot-user', 'Auth\ForgotUserController@send_mail');

//ตรวจสอบอีเมลตามเลขผู้เสียภาษี
Route::get('check-email/{tax_number?}', 'Auth\CheckEmailController@index');

//ลืมรหัสผ่าน
Route::POST('forgot-password', 'Auth\ForgotPasswordController@preResetLinkEmail');

//รีเซ็ตรหัสผ่านสำเร็จ
Route::get('password/reset_success', 'Auth\LoginController@reset_success');

//ติดต่อสมอ.
Route::get('contact', function () {
    return view('contact.index');
});


////รีเซ็ตอีเมล
Route::get('reset-email', function () {
    return view('auth.resetemail');
});

//คู่มือการใช้งาน
Route::get('manual', 'Auth\ManualController@index');

//พบปัญหาการใช้งาน
Route::get('help', function () {
    return view('auth.help.index');
});


Route::get('dashboard/e_accreditation', 'DashboardController@e_accreditation');

Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['admin', 'user']], function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    });
    Route::get('account-settings', 'UsersController@getSettings');
    Route::post('account-settings', 'UsersController@saveSettings');
});

Route::group(['middleware' => ['auth']], function () {

    //ใช้
    Route::get('dashboard', function () {
        return view('dashboard.dashboard');
    });

    //SSO Redirect
    Route::get('redirect/{id}', 'SSO\RedirectController@index');

    //โปรไฟล์
    Route::get('profile/show', 'ProfileController@show');
    Route::PATCH('profile/update/', 'ProfileController@update');

    //ยืนยันรับทราบข้อมูลที่อัปเดตจาก API กลาง (DBD/DOPA/RD) ก่อนไปปลายทาง (auto-login i-industry/ThaiD)
    Route::get('api-data-confirm', 'Auth\LoginController@showApiDataConfirm')->name('login.api_data_confirm');
    Route::get('api-data-confirm/proceed', 'Auth\LoginController@proceedApiDataConfirm')->name('login.api_data_confirm.proceed');

    //ตั้งค่า 2FA
    Route::get('profile/google2fa', 'ProfileController@google2fa');
    Route::post('profile/google2fa/disabled', 'ProfileController@google2fa_disabled');
    Route::post('profile/google2fa/enabled', 'ProfileController@google2fa_enabled');

    //เปลี่ยนรหัสผ่าน
    Route::get('profile/password', 'ProfileController@password');
    Route::post('profile/password_save', 'ProfileController@password_save');

    //เปลี่ยนภาพโปรไฟล์
    Route::get('profile/image-crop', 'ProfileController@imageCrop');
    Route::post('profile/image-crop', 'ProfileController@imageCropPost');

    #CRUD Generator
    Route::get('/crud-generator', ['uses' => 'ProcessController@getGenerator']);
    Route::post('/crud-generator', ['uses' => 'ProcessController@postGenerator']);

});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return redirect()->back();
});

//Funtion
Route::get('dashboard/request-section-5', 'Funtion\\FuntionsController@request_section_5');


//Auth
//Route::get('auth/{provider}/','Auth\SocialLoginController@redirectToProvider');
//Route::get('{provider}/callback','Auth\SocialLoginController@handleProviderCallback');
Route::get('logout', 'Auth\LoginController@logout');

//Google 2FA
Route::post('2fa/validate', 'Auth\LoginController@google2fa_validate'); //รับค่า 2fa หลัง login กรณีตั้งค่าไว้แล้ว
Route::post('2fa/setup', 'Auth\LoginController@google2fa_setup'); //รับค่า 2fa หลัง login กรณีบังคับใช้ 2fa แต่ผู้ใช้ยังไม่ได้ตั้งค่าไว้
Route::post('2fa/clear_session', 'Auth\LoginController@google2fa_clear_session'); //กดยกเลิก modal 2fa
//Route::get('enableTwoFactor','Auth\GoogleAuthenController@enableTwoFactor');//ทดสอบ

//บันทึกการตั้งค่า theme ของผู้ใช้งาน
Route::get('user/savetheme/{theme_name}', 'UsersController@savetheme');
Route::get('user/savefix-header/{fix_header}', 'UsersController@savefix_header');
Route::get('user/savefix-sidebar/{fix_sidebar}', 'UsersController@savefix_sidebar');
Route::get('user/update/type-sidebar/{type?}', 'UsersController@update_type_sidebar');

Route::POST('agents/delete_update', 'Agent\\AgentController@delete_update');
Route::POST('agents/up-act_instead', 'Agent\\AgentController@up_act_instead');
Route::POST('agents/update_instead_api', 'Agent\\AgentController@update_instead_api');//อัพเดทข้อมูลผู้มอบอำนาจจาก API
Route::get('agents/search-users-sso', 'Agent\\AgentController@search_users');
Route::resource('agents', 'Agent\\AgentController');

Route::resource('confirm-agents', 'Agent\\ConfirmAgentController');

Route::put('basic/branch-groups/update-state', 'Basic\BranchGroupController@update_state');
Route::resource('basic/branch-groups', 'Basic\\BranchGroupController');

//Route::get('request-section-5/application-inspection-unit/data_list', 'Section5\\ApplicationInspectionUnitController@data_list');
//Route::resource('request-section-5/application-inspection-unit', 'Section5\\ApplicationInspectionUnitController');

Route::get('request-section-5/application-lab/get-basic-tools/{test_item_id?}', 'Section5\\ApplicationLabController@GetBasicTools');
Route::get('request-section-5/application-lab/data_list_cer', 'Section5\\ApplicationLabController@data_list_cer');
Route::get('request-section-5/application-lab/data_list', 'Section5\\ApplicationLabController@data_list');
Route::get('request-section-5/application-lab/get-tis_name/{id?}', 'Section5\\ApplicationLabController@GetTisName');
Route::get('request-section-5/application-lab/get-test-tools-std/{id?}', 'Section5\\ApplicationLabController@GetTestItemToolsStd');
Route::get('request-section-5/application-lab/get-test-tools/{id?}', 'Section5\\ApplicationLabController@GetTestItemTools');
Route::get('request-section-5/application-lab/get-test-item/{tis_id?}', 'Section5\\ApplicationLabController@GetTestItem');
Route::get('request-section-5/application-lab/get-items-with-tools/{tis_id?}', 'Section5\\ApplicationLabController@GetAllItemsWithTools');
Route::POST('request-section-5/application-lab/save_test_tools', 'Section5\\ApplicationLabController@save_test_tools');
Route::POST('request-section-5/application-lab/delete_update', 'Section5\\ApplicationLabController@delete_update');
Route::get('request-section-5/application-lab/cancellation', 'Section5\\ApplicationLabCancellationController@cancellation');
Route::get('request-section-5/application-lab/cancellation/data_list', 'Section5\\ApplicationLabCancellationController@cancellation_data_list');
Route::get('request-section-5/application-lab/cancellation/details', 'Section5\\ApplicationLabCancellationController@cancellation_details');
Route::POST('request-section-5/application-lab/cancellation/save_cancellation', 'Section5\\ApplicationLabCancellationController@save_cancellation');
Route::get('request-section-5/application-lab/change-info', 'Section5\\ApplicationLabChangeInfoController@index');
Route::get('request-section-5/application-lab/change-info/data_list', 'Section5\\ApplicationLabChangeInfoController@data_list');
Route::get('request-section-5/application-lab/change-info/details', 'Section5\\ApplicationLabChangeInfoController@details');
Route::POST('request-section-5/application-lab/change-info/save', 'Section5\\ApplicationLabChangeInfoController@save');
Route::get('request-section-5/application-lab/manage-scope', 'Section5\\ApplicationLabController@manage_scope');
Route::get('request-section-5/labs', 'Section5\ApplicationLabController@labs_page');
Route::POST('request-section-5/application-lab/submit-scope-change', 'Section5\ApplicationLabController@submit_scope_change');
Route::POST('request-section-5/application-lab/submit-scope-final', 'Section5\ApplicationLabController@final_submit_scope');
Route::resource('request-section-5/application-lab', 'Section5\\ApplicationLabController');

Route::get('request_section5/application_inspectors/data_list', 'Section5\\ApplicationInspectorsController@data_list');
Route::get('request_section5/application-inspectors/search-users', 'Section5\\ApplicationInspectorsController@search_users');
Route::get('request_section5/application_inspectors/get-option-branch/{id?}', 'Section5\\ApplicationInspectorsController@getOptionBranch');
Route::get('request_section5/application_inspectors/search-users', 'Section5\\ApplicationInspectorsController@search_users');
Route::POST('request_section5/application_inspectors/delete_update', 'Section5\\ApplicationInspectorsController@delete_update');
Route::resource('request_section5/application_inspectors', 'Section5\\ApplicationInspectorsController');

Route::get('request-section-5/application-ibcb/get-standards/{type}', 'Section5\\ApplicationIbcbController@getStandards');
Route::get('request-section-5/application-ibcb/data_list', 'Section5\\ApplicationIbcbController@data_list');
Route::get('request-section-5/application-ibcb/get-branche-tis/{id?}', 'Section5\\ApplicationIbcbController@getDataBrancheTis');
Route::get('request-section-5/application-ibcb/get-branche/{id?}', 'Section5\\ApplicationIbcbController@getDataBranche');
Route::get('request-section-5/application-ibcb/getDataCertificate', 'Section5\\ApplicationIbcbController@getDataCertificate');
Route::get('request-section-5/application-ibcb/getDataInspectors', 'Section5\\ApplicationIbcbController@getDataInspectors');
Route::post('request-section-5/application-ibcb/getDataInspectors', 'Section5\\ApplicationIbcbController@getDataInspectors');
Route::POST('request-section-5/application-ibcb/delete_update', 'Section5\\ApplicationIbcbController@delete_update');
Route::get('request-section-5/ibcbs', 'Section5\\ApplicationIbcbController@ibcbs_page');
Route::get('request-section-5/ibcbs/show/{id}', 'Section5\\ApplicationIbcbController@ibcbs_show');
Route::POST('request-section-5/application-ibcb/submit-scope-final', 'Section5\\ApplicationIbcbController@final_submit_scope');
Route::get('request-section-5/application-ibcb/change-info', 'Section5\\ApplicationIbcbChangeInfoController@index');
Route::get('request-section-5/application-ibcb/change-info/data_list', 'Section5\\ApplicationIbcbChangeInfoController@data_list');
Route::get('request-section-5/application-ibcb/change-info/details', 'Section5\\ApplicationIbcbChangeInfoController@details');
Route::POST('request-section-5/application-ibcb/change-info/save', 'Section5\\ApplicationIbcbChangeInfoController@save');
Route::get('request-section-5/application-ibcb/cancellation', 'Section5\\ApplicationIbcbCancellationController@index');
Route::get('request-section-5/application-ibcb/cancellation/data_list', 'Section5\\ApplicationIbcbCancellationController@data_list');
Route::get('request-section-5/application-ibcb/cancellation/details', 'Section5\\ApplicationIbcbCancellationController@details');
Route::POST('request-section-5/application-ibcb/cancellation/save', 'Section5\\ApplicationIbcbCancellationController@save');
Route::resource('request-section-5/application-ibcb', 'Section5\\ApplicationIbcbController');

Route::resource('counsels', 'CounselsController');

// Section5 - Factory Inspection (รับคำขอตรวจโรงงาน - Full Functionality)
Route::get('section5/factory-inspection', 'Section5\\FactoryInspectionController@index');
Route::get('section5/factory-inspection/data_list', 'Section5\\FactoryInspectionController@data_list');
Route::get('section5/factory-inspection/approve/{id}', 'Section5\\FactoryInspectionController@approve');
Route::post('section5/factory-inspection/approve-save', 'Section5\\FactoryInspectionController@approve_save');
Route::post('section5/factory-inspection/result-save', 'Section5\\FactoryInspectionController@result_save');

// Section5 - Factory Inspection E-Surveillance (IB/CB ตรวจโรงงานตามแผน)
Route::get('section5/factory-inspection/esurveillance/check/{id}', 'Section5\\CsurvInspectionController@inspection_check');
Route::get('section5/factory-inspection/esurveillance/approve/{id}', 'Section5\\CsurvInspectionController@inspection_approve');
Route::post('section5/factory-inspection/esurveillance/approve-save', 'Section5\\CsurvInspectionController@inspection_approve_save');
Route::post('section5/factory-inspection/esurveillance/result-save', 'Section5\\CsurvInspectionController@inspection_result_save');

// Section5 - Product Testing (คำขอทดสอบผลิตภัณฑ์)
Route::get('section5/product-testing', 'Section5\\ProductTestingController@index');
Route::get('section5/product-testing/data_list', 'Section5\\ProductTestingController@data_list');
Route::get('section5/product-testing/detail/{id}', 'Section5\\ProductTestingController@detail');
Route::get('section5/product-testing/approve/{id}', 'Section5\\ProductTestingController@approve');
Route::post('section5/product-testing/approve-save', 'Section5\\ProductTestingController@approve_save');
Route::get('section5/product-testing/report/{id}', 'Section5\\ProductTestingController@report');
Route::post('section5/product-testing/report-save', 'Section5\\ProductTestingController@report_save')->name('product-testing.report-save');

// Section5 - Product Testing Surveillance (เฝ้าระวังทดสอบผลิตภัณฑ์)
Route::get('section5/product-testing-surveillance', 'Section5\\ProductTestingSurveillanceController@index');
Route::get('section5/product-testing-surveillance/data_list', 'Section5\\ProductTestingSurveillanceController@data_list');
Route::get('section5/product-testing-surveillance/approve/{id}', 'Section5\\ProductTestingSurveillanceController@approve');
Route::post('section5/product-testing-surveillance/approve-save', 'Section5\\ProductTestingSurveillanceController@approve_save');
Route::post('section5/product-testing-surveillance/result-save', 'Section5\\ProductTestingSurveillanceController@result_save');
Route::get('section5/product-testing-surveillance/report/{id}', 'Section5\\ProductTestingSurveillanceController@report')->name('product-testing-surveillance.report');
Route::post('section5/product-testing-surveillance/report-save', 'Section5\\ProductTestingSurveillanceController@report_save')->name('product-testing-surveillance.report-save');

Route::get('/test-tisi-url', function (Request $request) {
    try {
        $uid = 'abc123';
        $aid = 'xyz789';

        $prefix = strtolower(explode('/', trim($request->path(), '/'))[0] ?? '');
        $base = ($prefix === 'moiapitest')
            ? 'https://www4.tisi.go.th/moiapitest/LoginIndust.asp'
            : 'https://www3.tisi.go.th/moiapi/LoginIndust.asp';

        $url = $base . '?uid=' . urlencode($uid) . '&aid=' . urlencode($aid);

        return response()->json(['url' => $url]);
    } catch (\Exception $e) {
        Log::error('เกิดข้อผิดพลาดในการสร้าง TISI URL: ' . $e->getMessage());
        return response()->json([
            'error' => true,
            'message' => 'ไม่สามารถสร้าง URL ได้',
        ], 500);
    }
});

Route::get('/moiapi/proxy', function (Request $r) {
    $pid = (int) $r->query('pid', 0);
    $val = (string) $r->query('val', '');
    if ($pid <= 0 || $val === '') {
        return response()->json(['error' => 'bad_request'], 400);
    }

    $url = 'https://www3.tisi.go.th/moiapi/srv.asp?pid=' . $pid
        . '&refer=intelligist&val=' . rawurlencode($val);

    try {
        $headers = [
            'User-Agent' => 'Mozilla/5.0',
            'Accept' => 'application/json,text/plain,*/*',
            'Accept-Language' => 'th,en-US;q=0.7,en;q=0.3',
            'Connection' => 'close',
        ];
        $client = new Client(['timeout' => 12, 'http_errors' => false, 'verify' => true, 'headers' => $headers]);
        $resp = $client->get($url);

        return response($resp->getBody(), $resp->getStatusCode())
            ->header('Content-Type', $resp->getHeaderLine('Content-Type') ?: 'application/json')
            ->header('Cache-Control', 'no-store');
    } catch (\Throwable $e) {
        Log::warning('[moi-proxy] ' . $e->getMessage());
        return response()->json(['error' => 'upstream', 'message' => 'fetch_failed'], 502);
    }
})->name('moi.proxy');
