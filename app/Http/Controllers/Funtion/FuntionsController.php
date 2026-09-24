<?php

namespace App\Http\Controllers\Funtion;

use App\AccessLog;
use App\Http\Controllers\Controller;
use App\LoginLog;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

use App\Models\Agents\Agent;
use App\Models\Basic\Province;
use App\Models\Basic\District;
use App\Models\Basic\Subdistrict;
use App\Models\Basic\Zipcode;
use App\Models\Tis\Standard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;

use App\Models\Basic\BranchGroup;
use App\Models\Basic\Branch;
use App\Models\Basic\Tis;
use HP;

use App\Models\Log\LogNotification;
use App\Models\Section5\ApplicationInspectorStatus;
use App\Models\Section5\ApplicationLabStatus;
use App\Models\Section5\ApplicationIbcbStatus;

use App\Models\Section5\Labs;
use App\Models\Section5\LabsScope;
use App\Models\Section5\LabsScopeDetail;

use App\Models\Bsection5\TestItem;

use App\Models\Section5\Ibcbs;
use App\Sessions;
use Illuminate\Support\Facades\File;

use Carbon\Carbon;

class FuntionsController extends Controller
{

    public function __construct()
    {
        set_time_limit(0);
    }

    public function request_section_5()
    {
        if(Auth::check()){

            $user = auth()->user();

            //บันทึก Access Log
            $session_id = session()->getId();
            $config     = HP::getConfig();
            $app_name   = $config->sso_section5_app_name;

            $login_log  = LoginLog::where('session_id', $session_id)->first();
            if(!is_null($login_log)){
                $access_log = AccessLog::where('login_log_id', $login_log->id)
                                       ->orderBy('last_visit_at', 'desc')
                                       ->first();
                if(!is_null($access_log) && $access_log->app_name==$app_name){//พบรายการ Access Log ล่าสุด และเป็น section5
                    $time_diff = strtotime("now")-strtotime($access_log->last_visit_at);
                    if($time_diff>600){ //access log บันทึกไว้ตั้งแต่ 10 นาทีที่แล้ว ให้บันทึกเพิ่ม
                        Sessions::Modify($session_id, $app_name);
                    }
                }else{
                    Sessions::Modify($session_id, $app_name);
                }
            }

            $column_name = (new Tis)->getKeyName();

            //มาตรฐานที่ถูกยกเลิกของผู้ดูแล Lab
            $tis_ids = [];
            $lab_ids = [];
            $work_group_staffs = Labs::where('taxid', $user->tax_number)->get();

         //  dd($work_group_staffs);

            foreach ($work_group_staffs as $key => $work_group_staff) {

                if(count($work_group_staff->scope_standards)>0){
                    $work_group_tis_id = $work_group_staff->scope_standards->pluck('tis_id');
                    $tis_list = Tis::whereIn($column_name, $work_group_tis_id)
                                ->where('status', 5)
                                ->pluck($column_name)
                                ->toArray();
                    $tis_ids = array_merge($tis_ids, $tis_list);
                    $lab_ids[] = $work_group_staff->id;
                }
            }

          //  dd($tis_ids, $lab_ids);

            //Lab ที่มีมาตรฐานที่ถูกยกเลิก
            $labs = Labs::whereHas('scope_standards', function ($query) use ($tis_ids) {
                        $query->whereIn('tis_id', $tis_ids);
                    })
                    ->with('scope_standards')
                    ->whereIn('id', $lab_ids)
                    ->get(); 
            foreach($labs as $lab){
                //มอก.
                $lab->tis_amount = Tis::whereIn($column_name, $lab->scope_standards->pluck('tis_id'))
                                    ->where('status', 5)
                                    ->count();
                $lab->tis_name = Tis::whereIn($column_name, $lab->scope_standards->pluck('tis_id'))
                ->where('status', 5)
                ->selectRaw('CONCAT(tb3_Tisno," : ", tb3_TisThainame) as tb3_Tisno')
                ->pluck('tb3_Tisno')->implode('<br>');;
            }     

            //ขอบข่ายใกล้หมดอายุ น้อยกว่า 60วัน
            $tis_almost_expire_ids = [];
            $lab_almost_expire_ids = [];
            $lab_lists = Labs::where('taxid', $user->tax_number)->get();

            foreach ($lab_lists as $key => $lab_list) {
                if(count($lab_list->scope_standard_expire)>0){
                    $lab_list_tis_id = $lab_list->scope_standard_expire->pluck('tis_id');
                    $tis_list = Tis::whereIn($column_name, $lab_list_tis_id)
                                ->where('status', 1)
                                ->pluck($column_name)
                                ->toArray();
                    $tis_almost_expire_ids      = array_merge($tis_almost_expire_ids, $tis_list);
                    $lab_almost_expire_ids[]    = $lab_list->id;
                }
            }

            //Lab ที่มีขอบข่ายใกล้หมดอายุ
            $almost_expire = Labs::whereHas('scope_standard_expire', function ($query) use ($tis_almost_expire_ids) {
                        $query->whereIn('tis_id', $tis_almost_expire_ids);
                    })
                    ->with('scope_standard_expire')
                    ->whereIn('id', $lab_almost_expire_ids)
                    ->get(); 

            foreach($almost_expire as $almost_ex){
                //มอก.
                $almost_ex->tis_amount = Tis::whereIn($column_name, $almost_ex->scope_standard_expire->pluck('tis_id'))->where('status', 1)->count();
                $almost_ex->list_scope = LabsScope::where('lab_id', $almost_ex->id)->select('tis_id')->with('tis_standards')->groupBy('tis_id')->get();
            }   

          //  dd($almost_ex->list_scope);
   
            return view('dashboard/section5.index', compact('labs','almost_expire'));
        }
        return redirect('/');

    }

    public function GetDataTestItem(Request $request)
    {
        $tis_id = $request->get('tis_id');
        $lab_id = $request->get('lab_id');

        $test_item_id = LabsScope::where('lab_id', $lab_id)
                                ->with('test_item')
                                ->whereHas('test_item', function ($query) use($tis_id) {
                                    $query->where('tis_id', $tis_id);
                                })
                                ->select('test_item_id')
                                ->pluck('test_item_id', 'test_item_id')
                                ->toArray();

        $testitem = TestItem::Where('tis_id', $tis_id)
                            ->where('type',1)
                            ->where( function($query) use($lab_id, $tis_id){
                                $ids = DB::table((new LabsScope)->getTable().' AS scope')
                                            ->leftJoin((new TestItem)->getTable().' AS test', 'test.id', '=', 'scope.test_item_id')
                                            ->where('scope.lab_id', $lab_id )
                                            ->where('test.tis_id', $tis_id )
                                            ->select('test.main_topic_id');
                                $query->whereIn('id', $ids  );
                            })
                            //->groupBy('main_topic_id')
                            ->orderby('no')
                            ->get();

        $scope_list = LabsScope::where('lab_id', $lab_id)
                                ->whereHas('test_item', function ($query) use($tis_id) {
                                    $query->where('tis_id', $tis_id);
                                })
                                ->with(['test_item'])
                              
                                ->get()
                                ->keyBy('id');
        $level = 0;
        $list =   $this->LoopItem($testitem , $level, $test_item_id, $scope_list);

        return response()->json($list);
    }

    public function LoopItem($testitem , $level, $test_item_id, $scope_list)
    {
        $txt = [];
        $level++;
        $i = 0;
        $un_set_arr = [];
        $StateHtml = [ 1 => '<span class=" text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>' ];

        $expDate = Carbon::now()->subDays(60)->formatLocalized('%Y-%m-%d');

        // dd($expDate);

        foreach ( $testitem AS $item ){

            //กรณี ที่มี รายการทดสอบ เดียวกันมากกว่า 2 รายการ
            if( count($scope_list->where('test_item_id',$item->id )) >= 2 ){

                foreach( $scope_list->where('test_item_id',$item->id ) AS $scope ){

                    $expDate = Carbon::now()->subDays(60)->formatLocalized('%Y-%m-%d');
                    $date_end  =  !empty($scope->end_date)?'<span class="'.( ($scope->end_date < $expDate)? "text-danger" : "text-warning" ).'">Exp. '.HP::revertDate($scope->end_date,true).'</span>':null;

                    $active = array_key_exists( $scope->state , $StateHtml )? $StateHtml[ $scope->state ] : $StateHtml[ 2 ];

                    $btn = null;
                    if( !empty($scope->id) && $scope->test_item_id == $item->id ){
                        $btn =  ((( !empty($item->no)?'ข้อ '.$item->no.' ':'' ).$item->title));
                    }

                    $import = null;
                    if( $scope->type == 2 ){
                        $import = '<span class="text-muted"><em>(นำเข้าข้อมูลเมื่อ :'.(HP::revertDate($scope->created_at,true)).')</em></span>';
                    }

                    $remarks = null;
                    if( !empty($scope->remarks) ){
                        $remarks = '<span class="text-muted"><em>('.($scope->remarks).')</em></span>';
                    }

                    $key_on = $i++;

                    $data = new stdClass;
                    $data->text = (!empty($btn)?$btn:( (( !empty($item->no)?'ข้อ '.$item->no.' ':'' ).$item->title) )).(isset($remarks)?' '.$remarks:null).'<span class="pull-right">'.(isset($import)?' '.$import:null).(isset($date_end)?' '.$date_end:null).(isset($active)?' <span class="text-muted">|</span> '.$active:null).'</span>';
                    $data->href = '#parent_level_id-'.$item->id;
                    $result = $this->LoopItem($item->TestItemParentData, $level, $test_item_id, $scope_list);
                    $data->tags = [ count($result) ];
                    if(count( $result) >= 1 ){
                        $data->nodes =  $result;
                        $txt[] =   $data;
                    }else{
                        if( in_array( $item->id,  $test_item_id ) ){
                            $txt[] =   $data;
                        }
                    }
 
                }

            }else{

                //รายการทดสอบ จาก scope Lab
                $scope    = $scope_list->where('test_item_id',$item->id )->last();
                $import   = null;
                $remarks  = null;
                $btn      = null;
                $date_end = null;
                $active   = null;
                
                if( !empty($scope)){

                    $expDate = Carbon::now()->subDays(60)->formatLocalized('%Y-%m-%d');
                    $date_end  =  !empty($scope->end_date)?'<span class="'.( ($scope->end_date < $expDate)? "text-danger" : "text-warning" ).'">Exp. '.HP::revertDate($scope->end_date,true).'</span>':null;

                    if(  !empty($scope->state) ){
                        $active =  array_key_exists( $scope->state , $StateHtml )? $StateHtml[ $scope->state ] : $StateHtml[ 2 ];
                    }
         
                    if( !empty($scope->id) && $scope->test_item_id == $item->id ){
                        $btn = ((( !empty($item->no)?'ข้อ '.$item->no.' ':'' ).$item->title));
                    }

                    if( !empty($scope->type) && $scope->type == 2 ){
                        $import = '<span class="text-muted"><em>(นำเข้าข้อมูลเมื่อ :'.(HP::revertDate($scope->created_at,true)).')</em></span>';
                    }

                    if( !empty($scope->remarks) ){
                        $remarks = '<span class="text-muted"><em>('.($scope->remarks).')</em></span>';
                    }

                }

                $key_on = $i++;

                $data = new stdClass;
                $data->text = (!empty($btn)?$btn:( (( !empty($item->no)?'ข้อ '.$item->no.' ':'' ).$item->title) )).(isset($remarks)?' '.$remarks:null).'<span class="pull-right">'.(isset($import)?' '.$import:null).(isset($date_end)?' '.$date_end:null).(isset($active)?' <span class="text-muted">|</span> '.$active:null).'</span>';
                $data->href = '#parent_level_id-'.$item->id;
                $result = $this->LoopItem($item->TestItemParentData, $level, $test_item_id, $scope_list);
                $data->tags = [ count($result) ];
                if(count( $result) >= 1 ){
                    $data->nodes =  $result;
                    $txt[] =   $data;
                }else{
                    if( in_array( $item->id,  $test_item_id ) ){
                        $txt[] =   $data;
                    }
                }
    

            }

        }

        return $txt;
    }

    public function UserAgentExpire()
    {
        $agent = Agent::whereNotIn('state', [99])->whereDate('end_date', '<', date('Y-m-d') )->get();

        foreach( $agent as $item ){
            if( $item->issue_type == 2 && !empty($item->end_date) ){
                $item->state = 4;
                $item->save();
            }
        }
    }

    public function GetAddreess(Request $request, $zipcode_id){ // ดึงข้อมูลที่อยู่จาก รหัสไปรษณีย์

        $address_data = DB::table((new Zipcode)->getTable().' AS zip') //รหัสไปรษณีย์
                    ->leftJoin((new Subdistrict)->getTable().' AS sub', 'sub.DISTRICT_CODE', '=', 'zip.district_code') // ตำบล
                    ->leftJoin((new District)->getTable().' AS dis', 'dis.AMPHUR_ID', '=', 'sub.AMPHUR_ID') // อำเภอ
                    ->leftJoin((new Province)->getTable().' AS pro', 'pro.PROVINCE_ID', '=', 'sub.PROVINCE_ID')  // จังหวัด
                    ->where(function($query) use ($zipcode_id){
                        $query->where('zip.id', $zipcode_id);
                    })
                    ->where(function($query){
                        // $query->where(DB::raw("REPLACE(sub.DISTRICT_NAME,' ','')"),  'NOT LIKE', "%*%");
                        $query->whereNull('sub.state');
                    })
                    ->select(

                        DB::raw("sub.DISTRICT_ID AS sub_ids"),
                        DB::raw("TRIM(sub.DISTRICT_NAME) AS sub_title"),

                        DB::raw("dis.AMPHUR_ID AS dis_id"),
                        DB::raw("TRIM(dis.AMPHUR_NAME) AS dis_title"),

                        DB::raw("pro.PROVINCE_ID AS pro_id"),
                        DB::raw("TRIM(pro.PROVINCE_NAME) AS pro_title"),

                        DB::raw("zip.zipcode AS zip_code")

                    )
                    ->first();

            if(isset($request->khet) && $request->khet==1){
                $address_data->dis_title = !empty($address_data->dis_title) && mb_strpos($address_data->dis_title, 'เขต')===0 ? trim(mb_substr($address_data->dis_title, 3)) : $address_data->dis_title ; //ตัดคำว่าเขต คำแรกออก
            }

        return response()->json($address_data);
    }

    public function SearchAddreess(Request $request){//ค้นหาที่อยู่จากตัวSelect 2

        $searchTerm = !empty($request->searchTerm)?$request->searchTerm:null;
        $searchTerm = str_replace(' ', '', $searchTerm);

        $address_data  =  DB::table((new Zipcode)->getTable().' AS zip') //รหัสไปรษณีย์
                            ->leftJoin((new Subdistrict)->getTable().' AS sub', 'sub.DISTRICT_CODE', '=', 'zip.district_code') // อำเภอ
                            ->leftJoin((new District)->getTable().' AS dis', 'dis.AMPHUR_ID', '=', 'sub.AMPHUR_ID') // อำเภอ
                            ->leftJoin((new Province)->getTable().' AS pro', 'pro.PROVINCE_ID', '=', 'sub.PROVINCE_ID')  // จังหวัด
                            ->where(function($query) use($searchTerm){
                                $query->where(DB::raw("REPLACE(sub.DISTRICT_NAME,' ','')"), 'LIKE', "%$searchTerm%")
                                        ->orWhere(DB::raw("REPLACE(dis.AMPHUR_NAME,' ','')"), 'LIKE', "%$searchTerm%")
                                        ->orWhere(DB::raw("REPLACE(pro.PROVINCE_NAME,' ','')"), 'LIKE', "%$searchTerm%")
                                        ->orWhere(DB::raw("REPLACE(zip.zipcode,' ','')"), 'LIKE', "%$searchTerm%");
                            })
                            ->where(function($query){
                                // $query->where(DB::raw("REPLACE(sub.DISTRICT_NAME,' ','')"),  'NOT LIKE', "%*%");
                                $query->whereNull('sub.state');
                            })
                            ->select(

                                DB::raw("sub.DISTRICT_ID AS sub_ids"),
                                DB::raw("TRIM(sub.DISTRICT_NAME) AS sub_title"),

                                DB::raw("dis.AMPHUR_ID AS dis_id"),
                                DB::raw("TRIM(dis.AMPHUR_NAME) AS dis_title"),

                                DB::raw("pro.PROVINCE_ID AS pro_id"),
                                DB::raw("TRIM(pro.PROVINCE_NAME) AS pro_title"),

                                DB::raw("zip.zipcode AS sub_zip_code"),
                                DB::raw("zip.id AS id_zip_code")

                            )
                            ->get();
        $data_list = [];

        foreach($address_data as $datas){

            $address = '';

            if(  strpos( $datas->dis_title , 'เขต' ) !== false ||  strpos( $datas->sub_title , 'แขวง' ) !== false  ){
                $address .= 'แขวง'.$datas->sub_title.' | ';
            }else{
                $address .= 'ต.'.$datas->sub_title.' | ';
            }

            if( strpos( $datas->dis_title , 'เขต' ) !== false  ){
                $address .= ' '.$datas->dis_title.' | ';
            }else{
                $address .= ' อ.'.$datas->dis_title.' | ';
            }

            $address .= ' จ.'.$datas->pro_title.' | ';
            $address .= ' '.$datas->sub_zip_code;

            $data = new stdClass;
            $data->id = $datas->id_zip_code;
            $data->text = $address;

            $data_list[] = $data;
        }
        echo json_encode($data_list,JSON_UNESCAPED_UNICODE);
    }

    public function SearchStandards(Request $request){//ค้นหามาตรฐาน

        $search_query = $request->get('searchTerm');
        $searchTerm = str_replace(' ', '', $search_query);

        $data_std =  Tis::where(function($query) use($searchTerm){
                                    $query->Where(DB::raw("CONCAT(tb3_Tisno, ' : ', tb3_TisThainame)"), 'LIKE', "%".$searchTerm."%");
                                })
                                ->select('tb3_TisAutono', 'tb3_Tisno', 'tb3_TisThainame')
                                ->get();   

        $data_list = [];

        foreach($data_std as $datas){

            $tis_tisno = $datas->tb3_Tisno.' : '.($datas->tb3_TisThainame);

            $data            = new stdClass;
            $data->id        = $datas->getKey();
            $data->tis_tisno = $datas->tb3_Tisno;
            $data->name      = $tis_tisno;
            $data->text      = $tis_tisno;
            $data->title     = $datas->tb3_TisThainame;
            $data_list[]     = $data;
        }

        return response()->json($data_list);

    }

    public function setCookie(Request $request) {
        $minutes = time() + (20 * 365 * 24 * 60 * 60);
        $response = new Response('Set Cookie');
        $response->withCookie(cookie('active_cookie', 'active', $minutes));
        return $response;
    }

    public function getCookie(Request $request) {
        $value = $request->cookie('active_cookie');
        echo $value;
    }

    public function GetBranchData($id_group)
    {
        if( $id_group === 'ALL' ){
            $data =  Branch::get();
        }else{
            $data =  Branch::where('branch_group_id', $id_group )->get();
        }

        return response()->json($data);
    }

    public function getNotification()
    {
        $log = [];

        if( Schema::hasTable((new LogNotification)->getTable()) ){  //เช็คว่ามีตารางจริงใหม่
            
            if( Auth::check() ){
                $log = LogNotification::where('users_id',  auth()->user()->getKey() )->where('type',1)->orderby('created_at', 'desc')->limit('99')->get();
            }

            $arr_status_ibcb = [];
            if(  Schema::hasTable((new ApplicationIbcbStatus)->getTable()) ){ //เช็คว่ามีตารางจริงใหม่
                $arr_status_ibcb = ApplicationIbcbStatus::pluck('title', 'id')->toArray();
            }

            $arr_status_insp = [];
            if(  Schema::hasTable((new ApplicationInspectorStatus)->getTable()) ){ //เช็คว่ามีตารางจริงใหม่
                $arr_status_insp = ApplicationInspectorStatus::pluck('title', 'id')->toArray();
            }
       
            $arr_status_lab = [];
            if(  Schema::hasTable((new ApplicationLabStatus)->getTable()) ){ //เช็คว่ามีตารางจริงใหม่
                $arr_status_lab = ApplicationLabStatus::pluck('title', 'id')->toArray();
            }

            foreach( $log AS $item ){

                $item->created_ats = HP::dateTimeFormatN($item->created_at);
                if( $item->ref_table == 'section5_application_ibcb' ){
                   $item->ref_status =  array_key_exists( $item->status,  $arr_status_ibcb )?$arr_status_ibcb [ $item->status ]:'-';
                   $item->url = ('/request-section-5/application-ibcb');
                   $item->root_site = url('/');
                }else if( $item->ref_table == 'section5_application_inspectors' ){
                   $item->ref_status =  array_key_exists( $item->status,  $arr_status_insp )?$arr_status_insp [ $item->status ]:'-';
                   $item->url = ('/request_section5/application_inspectors');
                   $item->root_site = url('/');
                }else if( $item->ref_table == 'section5_application_labs' ){
                   $item->ref_status =  array_key_exists( $item->status,  $arr_status_lab )?$arr_status_lab [ $item->status ]:'-';
                   $item->url = ('/request-section-5/application-lab');
                   $item->root_site = url('/');
                }
            }
        }

        return response()->json($log);
    }

    public function Notification_redirect($id)
    {
        if( Schema::hasTable((new LogNotification)->getTable()) ){  //เช็คว่ามีตารางจริงใหม่
            $data = LogNotification::findOrFail($id);
            $data->update(['read' => 1]);
            if( $data->ref_table == 'section5_application_ibcb' ){
                $data->url = ('/request-section-5/application-ibcb');
                $data->root_site = url('/');
            }else if( $data->ref_table == 'section5_application_inspectors' ){
                $data->url = ('/request_section5/application_inspectors');
                $data->root_site = url('/');
            }else if( $data->ref_table == 'section5_application_labs' ){
                $data->url = ('/request-section-5/application-lab');
                $data->root_site = url('/');
            }
            return redirect($data->root_site.'/'.$data->url);
        }
    }

    public function NotificationReadAll(Request $request)
    {
        $id_array = $request->input('id');
        if( Schema::hasTable((new LogNotification)->getTable()) ){  //เช็คว่ามีตารางจริงใหม่
            $result = LogNotification::whereIn('id', $id_array);
            if($result->update(['read_all' => 1]))
            {
                echo 'Data Deleted';
            }
        }else{
            echo 'error';
        }
    }

    public function GetSection5Lab($lab_id)
    {
        $labs = Labs::where('id',$lab_id)->first();

        $data                     = new stdClass;
        $data->id                 = !is_null($labs)?$labs->id:null;
        $data->name               = !is_null($labs)?$labs->name:null;
        $data->taxid              = !is_null($labs)?$labs->taxid:null;
        $data->lab_code           = !is_null($labs)?$labs->lab_code:null;
        $data->lab_name           = !is_null($labs)?$labs->lab_name:null;
        $data->lab_phone          = !is_null($labs)?$labs->lab_phone:null;
        $data->lab_fax            = !is_null($labs)?$labs->lab_fax:null;

        $data->lab_address        = !is_null($labs)?$labs->lab_address:null;
        $data->lab_moo            = !is_null($labs)?$labs->lab_moo:null;
        $data->lab_soi            = !is_null($labs)?$labs->lab_soi:null;
        $data->lab_road           = !is_null($labs)?$labs->lab_road:null;
        $data->lab_building       = !is_null($labs)?$labs->lab_building:null;

        $data->lab_subdistrict_id = !is_null($labs)?$labs->lab_subdistrict_id:null;
        $data->lab_subdistrict    = !is_null($labs)?$labs->LabSubdistrictName:null;

        $data->lab_district_id    = !is_null($labs)?$labs->lab_district_id:null;
        $data->lab_district       = !is_null($labs)?$labs->LabDistrictName:null;

        $data->lab_province_id    = !is_null($labs)?$labs->lab_province_id:null;
        $data->lab_province       = !is_null($labs)?$labs->LabProvinceName:null;

        $data->lab_zipcode        = !is_null($labs)?$labs->lab_zipcode:null;

        return response()->json($data);

    }

    public function GetSection5LabScope($lab_id)
    {
        $labs = Labs::where('id',$lab_id)->first();

        $scope = LabsScope::Where('lab_id', $labs->id )->get()->groupBy('tis_id');

        $tis_tisno = LabsScope::Where('lab_id', $labs->id )
                                ->select('tis_id')
                                ->with( 
                                    ['tis_standards' => function ($query) {
                                        $query->select('id', DB::Raw('CONCAT_WS(" : ",tis_tisno, title) AS standard_title'));
                                    }]
                                )
                                ->groupBy('tis_id')
                                ->get()
                                ->pluck('tis_standards.standard_title', 'tis_standards.id')
                                ->toArray();

        return view('section5.application-lab.form.reduce-scope',compact('scope','tis_tisno'));

    }

    public function DataOptionIBCB(Request $request)
    {

        $applicant_taxid = $request->get('applicant_taxid');
        $applicant_type  = $request->get('applicant_type');
        
        $option_list = Ibcbs::where('ibcb_type', $applicant_type )
                            ->where(function ($query) use($applicant_taxid){
                                $query->where('taxid', $applicant_taxid );
                            })
                            ->select(DB::raw("CONCAT_WS(' : ', ibcb_code, IF( ibcb_name IS NULL, name, ibcb_name ) ) AS ibcb_title"), 'id')
                            ->orderBy('ibcb_code')
                            ->get();

        return response()->json($option_list);
            
    }

    public function GetSection5IBCB($ibcbs_id)
    {
        $ibcbs = Ibcbs::where('id',$ibcbs_id)->first();

        $data                      = new stdClass;
        $data->id                  = !is_null($ibcbs)?$ibcbs->id:null;
        $data->name                = !is_null($ibcbs)?$ibcbs->name:null;
        $data->taxid               = !is_null($ibcbs)?$ibcbs->taxid:null;
        $data->ibcb_code           = !is_null($ibcbs)?$ibcbs->ibcb_code:null;
        $data->ibcb_name           = !is_null($ibcbs)?$ibcbs->ibcb_name:null;
        $data->ibcb_phone          = !is_null($ibcbs)?$ibcbs->ibcb_phone:null;
        $data->ibcb_fax            = !is_null($ibcbs)?$ibcbs->ibcb_fax:null;

        $data->ibcb_address        = !is_null($ibcbs)?$ibcbs->ibcb_address:null;
        $data->ibcb_moo            = !is_null($ibcbs)?$ibcbs->ibcb_moo:null;
        $data->ibcb_soi            = !is_null($ibcbs)?$ibcbs->ibcb_soi:null;
        $data->ibcb_road           = !is_null($ibcbs)?$ibcbs->ibcb_road:null;
        $data->ibcb_building       = !is_null($ibcbs)?$ibcbs->ibcb_building:null;

        $data->ibcb_subdistrict_id = !is_null($ibcbs)?$ibcbs->ibcb_subdistrict_id:null;
        $data->ibcb_subdistrict    = !is_null($ibcbs)?$ibcbs->IbcbSubdistrictName:null;

        $data->ibcb_district_id    = !is_null($ibcbs)?$ibcbs->ibcb_district_id:null;
        $data->ibcb_district       = !is_null($ibcbs)?$ibcbs->IbcbDistrictName:null;

        $data->ibcb_province_id    = !is_null($ibcbs)?$ibcbs->ibcb_province_id:null;
        $data->ibcb_province       = !is_null($ibcbs)?$ibcbs->IbcbProvinceName:null;

        $data->ibcb_zipcode        = !is_null($ibcbs)?$ibcbs->ibcb_zipcode:null;

        $data->co_name             = !is_null($ibcbs)?$ibcbs->co_name:null;
        $data->co_position         = !is_null($ibcbs)?$ibcbs->co_position:null;
        $data->co_mobile           = !is_null($ibcbs)?$ibcbs->co_mobile:null;
        $data->co_phone            = !is_null($ibcbs)?$ibcbs->co_phone:null;
        $data->co_fax              = !is_null($ibcbs)?$ibcbs->co_fax:null;
        $data->co_email            = !is_null($ibcbs)?$ibcbs->co_email:null;

        return response()->json($data);

    }

    public function ClearLog(){
        echo File::delete(storage_path('logs\laravel.log'));
    }

}
