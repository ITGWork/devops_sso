<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use App\Models\Basic\Tis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use HP;
use App\User;

use App\Models\Section5\ApplicationLab;
use App\Models\Section5\ApplicationLabCertificate;
use App\Models\Section5\ApplicationLabScope;

use App\Models\Bsection5\TestMethod;
use App\Models\Bsection5\Unit;
use App\Models\Bsection5\TestTool;
use App\Models\Bsection5\TestItemTools;
use App\Models\Bsection5\TestItem;

use App\Models\Certificate\CertificateExport;
use App\Models\Certificate\CertiLab;

use stdClass;
use App\Models\Section5\Labs;

use App\Models\Section5\ApplicationLabAccept;

class ApplicationLabController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        set_time_limit(0);
        $this->middleware('auth');
        $this->attach_path = 'files/sso';
    }

    /**
     * ผู้ใช้ที่ล็อกอินอยู่ (หรือผู้ประกอบการที่ดำเนินการแทนอยู่) เป็นเจ้าของ LAB นี้หรือไม่
     * เกณฑ์เดียวกับหน้า list (taxid) และปุ่มเพิ่ม/ลดขอบข่าย (lab_user_id) — 1 บริษัทมีได้หลาย LAB (lab_code) เพราะ ros_users ซ้ำ
     */
    private function ownsLab(Labs $labs)
    {
        $user = auth()->user();
        if (is_null($user)) {
            return false;
        }

        $owner  = $user->ActInstead ?: $user; // null = ยื่นในนามตัวเอง
        $taxId  = trim((string) $owner->tax_number);

        if (!empty($labs->lab_user_id) && (int) $labs->lab_user_id === (int) $owner->getKey()) {
            return true;
        }

        return $taxId !== '' && trim((string) $labs->taxid) === $taxId;
    }

    public function labs_page(Request $request)
    {
        $lab_id = $request->get('lab_id');
        $item_no = $request->get('item_no');
        return view('section5.labs.index', compact('lab_id', 'item_no'));
    }

    public function data_list(Request $request)
    {

        $user = auth()->user();
        $applicationLabTable = (new ApplicationLab)->getTable();
        $applicationLabScopeTable = (new ApplicationLabScope)->getTable();
        $applicationLabStatusTable = (new \App\Models\Section5\ApplicationLabStatus)->getTable();
        $tisTable = (new Tis)->getTable();
        $userTable = (new User)->getTable();

        $filter_search = $request->get('filter_search');
        $filter_state = $request->get('filter_state');

        $query = ApplicationLab::query()
                                        ->select($applicationLabTable.'.*')
                                        ->leftJoin($userTable.' as created_users', 'created_users.id', '=', $applicationLabTable.'.created_by')
                                        ->leftJoin($userTable.' as agent_users', 'agent_users.id', '=', $applicationLabTable.'.agent_id')
                                        ->leftJoin($applicationLabStatusTable.' as application_statuses', 'application_statuses.id', '=', $applicationLabTable.'.application_status')
                                        ->selectRaw('COALESCE(agent_users.name, created_users.name) AS creater_sort')
                                        ->selectRaw('application_statuses.title AS status_sort')
                                        ->selectSub(function ($subQuery) use ($applicationLabScopeTable, $tisTable, $applicationLabTable) {
                                            $subQuery->from($applicationLabScopeTable.' as app_scope')
                                                     ->leftJoin($tisTable.' as tis', 'tis.tb3_TisAutono', '=', 'app_scope.tis_id')
                                                     ->whereColumn('app_scope.application_lab_id', $applicationLabTable.'.id')
                                                     ->selectRaw("GROUP_CONCAT(DISTINCT tis.tb3_Tisno ORDER BY tis.tb3_Tisno SEPARATOR ', ')");
                                        }, 'standards_sort')
                                        ->when( $filter_search , function ($query, $filter_search){
                                            $search_full = str_replace(' ', '', $filter_search);

                                            if( strpos( $search_full , 'LAB-' ) !== false){
                                                return $query->where('application_no',  'LIKE', "%$search_full%");
                                            }else{
                                                return  $query->where(function ($query2) use($search_full) {
                                                                    $query2->Where(DB::raw("REPLACE(applicant_name,' ','')"), 'LIKE', "%".$search_full."%")
                                                                            ->OrWhere(DB::raw("REPLACE(applicant_taxid,' ','')"), 'LIKE', "%".$search_full."%")
                                                                            ->OrWhere(DB::raw("REPLACE(lab_name,' ','')"), 'LIKE', "%".$search_full."%")
                                                                            ->Orwhere('application_no',  'LIKE', "%$search_full%");

                                                                });
                                            }
                                        })
                                        ->when($filter_state, function ($query, $filter_state){
                                            return $query->where('application_status', $filter_state );
                                        })
                                        ->when($user, function($query, $user){//ผปก.ยื่นเอง หรือไปยื่นแทนคนอื่น

                                            $user_act_instead = $user->ActInstead;//ดำเนินการแทนผปก.คนไหน null=ยื่นของตัวเอง
                                            if(is_null($user_act_instead)){//อยู่ในฐานะตัวเอง แสดงคำขอของตัวเอง
                                                $query->where('created_by', $user->getKey());
                                            }else{//อยู่ในฐานะตัวแทน แสดงที่ตัวเองยื่นในฐานนะตัวแทน และเป็นของผปก.ที่กำลังเป็นตัวแทนอยู่ขณะนี้
                                                $query->where('agent_id', $user->getKey())
                                                      ->where('created_by', $user_act_instead->getKey());
                                            }

                                        });

        return Datatables::of($query)
                            ->addIndexColumn()
                            ->addColumn('application_no', function ($item) {

                                $application_type_arr = [ 1 => 'ขอขึ้นทะเบียนใหม่', 2 => 'ขอเพิ่มเติมขอบข่าย', 3 => 'ขอลดขอบข่าย', 4 => 'ขอแก้ไขข้อมูล', 5 => 'ขอยกเลิก', 6 => 'เปลี่ยนแปลง'];
                                $application_type = array_key_exists( $item->applicant_type,  $application_type_arr )?$application_type_arr [ $item->applicant_type ]:'-';

                                return '<div>'.(!empty($item->application_no)?$item->application_no:'-').'</div>'.(!empty($application_type)?'('.$application_type.')':'-');

                            })
                            ->addColumn('applicant_name', function ($item) {
                                return '<div>'.(!empty($item->lab_name)?$item->lab_name:'-').'</div>'.(!empty($item->applicant_name)?'('.$item->applicant_name.')':'-');
                            })
                            ->addColumn('applicant_taxid', function ($item) {
                                return !empty($item->applicant_taxid)?$item->applicant_taxid:'-';
                            })
                            ->addColumn('standards', function ($item) {
                                return !empty($item->standards_sort)?$item->standards_sort:'-';
                            })
                            ->addColumn('creater', function ($item) {
                                return !empty($item->creater_sort) ? $item->creater_sort : $item->CreaterName;
                            })
                            ->addColumn('application_date', function ($item) {
                                return !empty($item->application_date)?HP::DateThai($item->application_date):'-';
                            })
                            ->addColumn('status_application', function ($item) {
                                if( !empty($item->delete_state) ){
                                    $statusTitle = !empty($item->StatusTitle) ? $item->StatusTitle : (!empty($item->status_sort) ? $item->status_sort : null);
                                    return (!empty($statusTitle)?'<div class="text-danger">'.$statusTitle.'<div>':'-').'<div><em>'.(!empty($item->delete_at)?HP::DateThai($item->delete_at):null).'</em><div>';
                                }else{
                                    return !empty($item->StatusTitle)?$item->StatusTitle:(!empty($item->status_sort) ? $item->status_sort : 'ฉบับร่าง');
                                }
                            })
                            ->addColumn('action', function ($item) {
                                $edit = true;
                                $disabled = [];
                                if(!in_array($item->application_status, [2, 0, 15, 16])){
                                    $edit = false;
                                    array_push($disabled, 'edit');
                                }

                                $created_at = !empty($item->created_at) ? HP::DateTimeThai($item->created_at) : null;

                                $button = HP::buttonAction($item->id, 'request-section-5/application-lab', 'Section5\\ApplicationLabController@destroy', 'application-lab',  true, $edit, false, $disabled);
                                if( $item->application_status <= 13){
                                    $button .= " <button type='button' class='btn btn-danger btn-xs btn_delete' title='Delete application_delete'
                                                        data-id='{$item->id}'
                                                        data-application_no='{$item->application_no}'
                                                        data-applicant_name='{$item->applicant_name}'
                                                        data-applicant_taxid='{$item->applicant_taxid}'
                                                        data-created_at='{$created_at}'
                                                    ><i class='fa fa-trash-o' aria-hidden='true'></i>
                                                </button>";
                                }else{
                                    $button .= " <button type='button' class='btn btn-danger btn-xs btn_delete' disabled><i class='fa fa-trash-o' aria-hidden='true'></i></button>";
                                }

                                return $button;
                            })
                            ->orderColumn('application_no', $applicationLabTable.'.application_no $1')
                            ->orderColumn('applicant_name', $applicationLabTable.'.lab_name $1, '.$applicationLabTable.'.applicant_name $1')
                            ->orderColumn('applicant_taxid', $applicationLabTable.'.applicant_taxid $1')
                            ->orderColumn('standards', 'standards_sort $1')
                            ->orderColumn('creater', 'creater_sort $1')
                            ->orderColumn('application_date', 'COALESCE('.$applicationLabTable.'.application_date, '.$applicationLabTable.'.created_at) $1')
                            ->orderColumn('status_application', $applicationLabTable.'.application_status $1, '.$applicationLabTable.'.delete_state $1')
                            ->rawColumns(['checkbox', 'action', 'status_application','applicant_name','application_no'])
                            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $user_act_instead = $user->ActInstead;
        $acting_user = is_null($user_act_instead) ? $user : $user_act_instead;

        // มี lab ที่ยังใช้งานอยู่ (state=1) ไหม — เทียบทั้ง lab_user_id (ผู้ยื่นเดิม) และเลขนิติบุคคลเดียวกับที่หน้ารายชื่อ
        // (/request-section-5/labs → data_labs_list filter_trader) ใช้กรอง เพราะ lab เก่าบางตัว lab_user_id ว่าง
        $hasRegisteredLab = Labs::where('state', 1)->where(function ($q) use ($acting_user) {
            $q->where('lab_user_id', $acting_user->getKey());
            if (!empty($acting_user->tax_number)) {
                $q->orWhere('taxid', $acting_user->tax_number);
            }
        })->exists();

        return view('section5/application-lab.index', compact('hasRegisteredLab'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('section5/application-lab.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $requestData = $request->all();

            $user_act_instead = auth()->user()->ActInstead;//ดำเนินการแทนผปก.คนไหน null=ยื่นของตัวเอง

            $requestData['created_by'] = is_null($user_act_instead) ? auth()->user()->getKey() : $user_act_instead->getKey();
            $requestData['agent_id']   = is_null($user_act_instead) ? null : auth()->user()->getKey();//ผู้ดำเนินการแทน

            if( !empty( $requestData['type_save'] ) && $requestData['type_save'] == "save" ){ //ยื่นคำขอ อยู่ระหว่างการตรวจสอบ → ออกเลขที่คำขอ

                $gen_number =  HP::ConfigFormat( 'APP-LAB' , (new ApplicationLab)->getTable()  , 'application_no', null , null,null );
                $application_check = ApplicationLab::where('application_no', $gen_number)->first();
                if(!is_null($application_check)){
                    $gen_number =  HP::ConfigFormat( 'APP-LAB' , (new ApplicationLab)->getTable()  , 'application_no', null , null,null );
                }

                $requestData['application_no'] = $gen_number;
                $requestData['application_status'] = 1;
                $requestData['application_date'] = date('Y-m-d');
            }else{ //ฉบับร่าง → ยังไม่ออกเลขที่คำขอ
                $requestData['application_no'] = null;
                $requestData['application_status'] = 0;
            }

            $requestData['applicant_date_niti'] = !empty($requestData['applicant_date_niti'])?$requestData['applicant_date_niti']:null;

            // map applicant_type_display → audit_type (fallback กรณี JS ไม่ทำงาน)
            // 3 = ใบรับรอง 17025 → audit_type 1, 4 = ภาคผนวก ก. → audit_type 2
            if( empty($requestData['audit_type']) && !empty($requestData['applicant_type_display']) ){
                $requestData['audit_type'] = ($requestData['applicant_type_display'] == 3) ? 1 : 2;
            }

            $requestData['config_evidencce']  = (count(HP::ConfigEvidence(3)) > 0)?json_encode(HP::ConfigEvidence(3), JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE):null;

            //กรณีมี ID section5_labs id
            if( !empty( $requestData['lab_id'] ) ){
                $labs = Labs::where('id', $requestData['lab_id'] )->first();
                $requestData['lab_id']   = !empty($labs->id)?$labs->id:null;
                $requestData['lab_code'] = !empty($labs->lab_code)?$labs->lab_code:null;
            } elseif( !empty( $requestData['lab_code'] ) ) {
                // fallback: ถ้าไม่มี lab_id แต่มี lab_code ให้ lookup จาก DB
                $labs = Labs::where('lab_code', $requestData['lab_code'])->first();
                $requestData['lab_id']   = !empty($labs->id)?$labs->id:null;
                $requestData['lab_code'] = !empty($labs->lab_code)?$labs->lab_code:null;
            } else {
                $requestData['lab_id']   = null;
                $requestData['lab_code'] = null;
            }

            $application = ApplicationLab::create($requestData);

            $LabAcceptData                          = [];
            $LabAcceptData['application_lab_id']    = $application->id;
            $LabAcceptData['application_no']        = $requestData['application_no'];
            $LabAcceptData['application_status']    = $requestData['application_status'];
            $LabAcceptData['description']           = !empty($requestData['edit_detail']) ? $requestData['edit_detail'] : null;
            $LabAcceptData['appointment_date']      = 'edit_page';
            $LabAcceptData['created_by']            = auth()->user()->getKey();
            $LabAcceptData['created_at']            = date('Y-m-d H:i:s');

            ApplicationLabAccept::create($LabAcceptData);

            $this->SaveScope( $application, $requestData  );
            $this->SaveAudit( $application, $requestData  );

            $this->SaveFile( $application, $request );

            DB::commit();
            return redirect('request-section-5/application-lab')->with('flash_message', 'เรียบร้อยแล้ว!');

        } catch (\Throwable $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            \Log::error('Section5 LAB applicant request failed', [
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);
            return redirect()->back()
                ->withInput($request->except(['evidences', 'repeater-file-other', 'evidence_file_config', 'evidence_file_other']))
                ->with('message_error', 'เกิดข้อผิดพลาดในการบันทึกคำขอ กรุณาลองใหม่อีกครั้ง หากยังพบปัญหากรุณาแจ้งเจ้าหน้าที่');
        }
    }

    public function SaveScope( $application ,  $requestData )
    {

        if( isset($requestData['section_box_tis']) ){

            $section_box_tis = $requestData['section_box_tis'];

            $section_ids = [];
            foreach( $section_box_tis AS $box ){
                $section_ids[$box] = $box;
            }

            $section_id = array_diff($section_ids, [null]);

            ApplicationLabScope::where('application_lab_id', $application->id)
                            ->whereNotNull('tis_id')
                            ->when($section_id, function ($query, $section_id){
                                return $query->whereNotIn('tis_id', $section_id);
                            })
                            ->delete();

            foreach( $section_box_tis AS $box ){

                if(  isset($requestData['repeater-group-'.$box ]) ){

                    $repeater_scope = $requestData['repeater-group-'.$box];

                    $list_scope_id = [];
                    foreach($repeater_scope as $scope){
                        if( isset( $scope['scope_id']) ){
                            $list_scope_id[] = $scope['scope_id'];
                        }
                    }
                    $list_ids = array_diff($list_scope_id, [null]);

                    ApplicationLabScope::where('application_lab_id', $application->id)
                                        ->when($list_ids, function ($query, $list_ids){
                                            return $query->whereNotIn('id', $list_ids);
                                        })
                                        ->where('tis_id', $box )
                                        ->delete();

                    foreach( $repeater_scope as $scope ){

                        if( array_key_exists('test_item_id', $scope ) && !empty($scope['test_item_id'])){
                            $scopes =  array_key_exists('scope_id', $scope) ? ApplicationLabScope::where('id',  $scope["scope_id"] )->first() : null ;
                            if(is_null($scopes)){
                                $scopes = new ApplicationLabScope;
                            }
                            $scopes->application_lab_id = $application->id;
                            $scopes->application_no     = $application->application_no;
                            $scopes->tis_id             = !empty($scope['tis_id'])?$scope['tis_id']:null;
                            $scopes->tis_tisno          = !empty($scope['tis_tisno'])?$scope['tis_tisno']:null;
                            $scopes->test_item_id       = !empty($scope['test_item_id'])?$scope['test_item_id']:null;
                            $test_tools_id = !empty($scope['test_tools_id']) ? $scope['test_tools_id'] : null;
                            if (is_null($test_tools_id) && !empty($scope['test_tools_custom_name'])) {
                                $customName = trim($scope['test_tools_custom_name']);
                                $existTool  = TestTool::where(DB::raw("REPLACE(title,' ','')"), str_replace(' ', '', $customName))->first();
                                if (is_null($existTool)) {
                                    $existTool = TestTool::create(['title' => $customName, 'state' => 1, 'created_by' => 0]);
                                }
                                $test_tools_id = $existTool->id;
                                TestItemTools::firstOrCreate([
                                    'bsection5_test_item_id' => $scope['test_item_id'],
                                    'test_tools_id'          => $test_tools_id,
                                ]);
                            }
                            $scopes->test_tools_id      = $test_tools_id;
                            $scopes->test_tools_no      = !empty($scope['test_tools_no'])?$scope['test_tools_no']:null;
                            $scopes->capacity           = !empty($scope['capacity'])?$scope['capacity']:null;
                            $scopes->range              = !empty($scope['range'])?$scope['range']:null;
                            $scopes->true_value         = !empty($scope['true_value'])?$scope['true_value']:null;
                            $scopes->fault_value        = !empty($scope['fault_value'])?$scope['fault_value']:null;
                            $scopes->test_duration      = !empty($scope['test_duration'])?$scope['test_duration']:null;
                            $scopes->test_price         = !empty($scope['test_price'])?$scope['test_price']:null;
    
                            //กรณีมี ID section5_labs id 
                            $scopes->lab_id             = !empty($application->lab_id)?$application->lab_id:null;
                            $scopes->lab_code           = !empty($application->lab_code)?$application->lab_code:null;
    
                            $scopes->save();
                        }

                    }

                }

            }

        }

    }

    public function SaveAudit($application ,  $requestData)
    {
        $audit_types = !empty($requestData['audit_type'])?$requestData['audit_type']:null;

        if( $audit_types  == 2 ){

            if( isset($requestData['repeater-audit-2']) ){

                $list_audit = [];

                foreach( $requestData['repeater-audit-2'] as $item ){
                    $data = new stdClass;
                    $data->audit_date_start =  !empty($item['audit_date_start'])?HP::convertDate($item['audit_date_start']):null;
                    $data->audit_date_end   =  !empty($item['audit_date_end'])?HP::convertDate($item['audit_date_end']):null;
                    $list_audit[] = $data;
                }

                $application->audit_date  =  (count($list_audit) > 0 )? json_encode($list_audit,JSON_UNESCAPED_UNICODE):null;
                $application->save();
            }else{
                $application->audit_date = null;
                $application->save();
            }

            ApplicationLabCertificate::where('application_lab_id', $application->id)->delete();


        }else{

            if( isset($requestData['repeater-audit-1']) ){

                
                $attach_path =  $this->attach_path.'/Section5/ApplicationLab/'.$application->application_no;

                $application->audit_date = null;
                $application->save();

                $repeater_audit_1 = $requestData['repeater-audit-1'];

                $list_id = [];
                foreach($repeater_audit_1 as $item){
                    if( isset( $item['cer_id']) ){
                        $list_id[] = $item['cer_id'];
                    }
                }
                $list_ids = array_diff($list_id, [null]);

                ApplicationLabCertificate::where('application_lab_id', $application->id)
                                            ->when($list_ids, function ($query, $list_ids){
                                                return $query->whereNotIn('id', $list_ids);
                                            })
                                            ->delete();

                foreach( $repeater_audit_1 as $item ){

                    $cer = array_key_exists('cer_id', $item) ? ApplicationLabCertificate::where('id', $item["cer_id"] )->first() : null ;
                    if(is_null($cer)){
                        $cer = new ApplicationLabCertificate;
                    }
                    $cer->application_lab_id     = $application->id;
                    $cer->application_no         = $application->application_no;
                    $cer->certificate_id         = !empty($item['certificate_id'])?$item['certificate_id']:null;
                    $cer->certificate_no         = !empty($item['certificate_no'])?$item['certificate_no']:null;
                    $cer->certificate_start_date =  !empty($item['certificate_start_date'])?HP::convertDate($item['certificate_start_date']):null;
                    $cer->certificate_end_date   =  !empty($item['certificate_end_date'])?HP::convertDate($item['certificate_end_date']):null;
                    $cer->issued_by              = isset($item['issued_by'])?1:2;
                    $cer->accereditatio_no       = !empty($item['accereditatio_no'])?$item['accereditatio_no']:null;
                    $cer->save();
                    
                    if( isset( $item['certificate_file'] ) && !empty($item['certificate_file']) ){

                        HP::singleFileUpload(

                            $item['certificate_file'],
                            $attach_path,
                            (auth()->user()->tax_number ?? null),
                            (auth()->user()->username ?? null),
                            'SSO',
                            (  (new ApplicationLabCertificate)->getTable() ),
                            $cer->id,
                            'audit_certificate_file',
                            null,
                            null

                        );

                    }


                }

            }

        }

    }


    public function SaveFile( $application ,  $request)
    {

        $requestData = $request->all();
        $applicant_taxid = auth()->user()->tax_number ?? $application->applicant_taxid;

        $attach_path =  $this->attach_path.'/Section5/ApplicationLab/'.$application->application_no;

        if( isset( $requestData['evidences'] ) && !empty($applicant_taxid) && is_numeric($applicant_taxid) ){

            $evidences = $requestData['evidences'];

            foreach( $evidences as $evidence ){

                if( isset( $evidence['evidence_file_config'] ) && !empty($evidence['evidence_file_config']) ){

                    HP::singleFileUpload(

                        $evidence['evidence_file_config'],
                        $attach_path,
                        (auth()->user()->tax_number ?? null),
                        (auth()->user()->username ?? null),
                        'SSO',
                        (  (new ApplicationLab)->getTable() ),
                        $application->id,
                        'evidence_file_config',
                        !empty($evidence['setting_title'])?$evidence['setting_title']:null,
                        !empty($evidence['setting_id'])?$evidence['setting_id']:null

                    );

                }

            }

        }

        if( !empty( $requestData['repeater-file-other'] ) ){

            $repeater_file = $requestData['repeater-file-other'];

            foreach( $repeater_file as $key=>$file ){

                if($request->hasFile("repeater-file-other.{$key}.evidence_file_other")){
                    HP::singleFileUpload(
                        $request->file("repeater-file-other.{$key}.evidence_file_other"),
                        $attach_path,
                        (auth()->user()->tax_number ?? null),
                        (auth()->user()->username ?? null),
                        'SSO',
                        (  (new ApplicationLab)->getTable() ),
                        $application->id,
                        'evidence_file_other',
                        $request->input("repeater-file-other.{$key}.file_documents")
                    );
                }

            }

        }

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $applicationlab = ApplicationLab::findOrFail($id);

        // applicant_type 5 = คำขอยกเลิกการแต่งตั้ง ใช้หน้าจอ/ฟิลด์คนละชุดกับคำขอปกติ (ดู edit() เช่นกัน)
        // เปิดแบบ readonly เพราะ "ดูรายละเอียด" ต้องดูได้ทุกสถานะ ไม่ใช่แค่สถานะที่แก้ไขได้เหมือน edit()
        if ($applicationlab->applicant_type == 5) {
            return redirect(url('/request-section-5/application-lab/cancellation/details')
                . '?lab_id=' . $applicationlab->lab_id . '&application_id=' . $applicationlab->id . '&readonly=1');
        }

        // applicant_type 2/3/4 = ขอเพิ่ม/ลด/ผสมขอบข่าย ยื่นผ่านหน้า labs/show (modal เพิ่ม/ลดขอบข่าย)
        // ไม่มีหน้าจอ readonly แยกต่างหาก พาไปหน้าจัดการขอบข่ายของ lab นั้นแทนดีกว่าเปิดฟอร์มผิด
        // ส่ง application_id ไปด้วยเสมอ (เจาะจงคำขอนี้) — labs_show() ต้องรู้ว่ากำลังแก้ไขคำขอไหนอยู่จริงๆ
        // ไม่ใช่เดา "ฉบับร่างล่าสุด" เอง ไม่งั้นกด "เพิ่ม/ลดขอบข่าย" ใหม่จากหน้า labs/show ตรงๆ (ไม่ผ่าน edit())
        // จะดันไปทับคำขอเก่าที่ถูกตีกลับโดยไม่ได้ตั้งใจ (มิเรอร์บั๊กเดียวกับที่แก้ใน IBCB ibcbs_show())
        if (in_array($applicationlab->applicant_type, [2, 3, 4]) && !empty($applicationlab->lab_id)) {
            return redirect(url('/request-section-5/labs/show/' . $applicationlab->lab_id)
                . '?application_id=' . $applicationlab->id);
        }

        // applicant_type 6 = ขอเปลี่ยนแปลงข้อมูลหน่วยตรวจสอบ
        if ($applicationlab->applicant_type == 6 && !empty($applicationlab->lab_id)) {
            return redirect(url('/request-section-5/application-lab/change-info/details')
                . '?lab_id=' . $applicationlab->lab_id . '&application_id=' . $applicationlab->id . '&readonly=1');
        }

        $applicationlab->edited = true;
        return view('section5/application-lab.show', compact('applicationlab'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $applicationlab = ApplicationLab::findOrFail($id);

        // applicant_type 5 = คำขอยกเลิกการแต่งตั้ง ใช้หน้าจอ/ฟิลด์คนละชุดกับคำขอปกติ
        // ต้องพาไปหน้า cancellation/details แทนฟอร์มคำขอทั่วไป
        if ($applicationlab->applicant_type == 5) {
            return redirect(url('/request-section-5/application-lab/cancellation/details')
                . '?lab_id=' . $applicationlab->lab_id . '&application_id=' . $applicationlab->id);
        }

        // applicant_type 2/3/4 = ขอเพิ่ม/ลด/ผสมขอบข่าย แก้ไขผ่านหน้า labs/show (modal เพิ่ม/ลดขอบข่าย)
        // ไม่ใช่ฟอร์มคำขอทั่วไป
        // ส่ง application_id ไปด้วยเสมอ (เจาะจงคำขอนี้) — labs_show() ต้องรู้ว่ากำลังแก้ไขคำขอไหนอยู่จริงๆ
        // ไม่ใช่เดา "ฉบับร่างล่าสุด" เอง ไม่งั้นกด "เพิ่ม/ลดขอบข่าย" ใหม่จากหน้า labs/show ตรงๆ (ไม่ผ่าน edit())
        // จะดันไปทับคำขอเก่าที่ถูกตีกลับโดยไม่ได้ตั้งใจ (มิเรอร์บั๊กเดียวกับที่แก้ใน IBCB ibcbs_show())
        if (in_array($applicationlab->applicant_type, [2, 3, 4]) && !empty($applicationlab->lab_id)) {
            return redirect(url('/request-section-5/labs/show/' . $applicationlab->lab_id)
                . '?application_id=' . $applicationlab->id);
        }

        // applicant_type 6 = ขอเปลี่ยนแปลงข้อมูลหน่วยตรวจสอบ
        if ($applicationlab->applicant_type == 6 && !empty($applicationlab->lab_id)) {
            return redirect(url('/request-section-5/application-lab/change-info/details')
                . '?lab_id=' . $applicationlab->lab_id . '&application_id=' . $applicationlab->id);
        }

        $applicationlab->show = true;
        $applicationlab->edit_page = true;
        return view('section5/application-lab.edit', compact('applicationlab'));
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
        DB::beginTransaction();
        try {

            $application = ApplicationLab::findOrFail($id);

            $requestData = $request->all();

            if( empty($application->application_no) ){
                $requestData['created_by'] = auth()->user()->getKey();
                if( !empty( $requestData['type_save'] ) && $requestData['type_save'] == "save" ){ //ยื่นคำขอ → ออกเลขที่คำขอ

                    $gen_number =  HP::ConfigFormat( 'APP-LAB' , (new ApplicationLab)->getTable()  , 'application_no', null , null,null );
                    $application_check = ApplicationLab::where('application_no', $gen_number)->first();
                    if(!is_null($application_check)){
                        $gen_number =  HP::ConfigFormat( 'APP-LAB' , (new ApplicationLab)->getTable()  , 'application_no', null , null,null );
                    }
                    $requestData['application_no'] = $gen_number;

                    $requestData['application_status'] = 1;
                    if(is_null($application->application_date)){
                        $requestData['application_date'] = date('Y-m-d');
                    }
                }else{ //ฉบับร่าง → ยังไม่ออกเลขที่คำขอ
                    unset($requestData['application_no']);
                    $requestData['application_status'] = 0;
                }
            }else{
                if( !empty( $requestData['type_save'] ) && $requestData['type_save'] == "save" ){
                    // ทุกกรณีที่ ผปก.แก้ไขแล้วส่งกลับ ให้เข้าคิวตรวจสอบปกติที่สถานะ 1
                    $requestData['application_status'] = 1;
                    if(is_null($application->application_date)){
                        $requestData['application_date'] = date('Y-m-d');
                    }
                }else{
                    $requestData['application_status'] = 0;
                }
                $requestData['updated_by'] = auth()->user()->getKey();
                $requestData['updated_at'] = date('Y-m-d H:i:s');
            }

            // Resubmitting an existing request must keep its original number.
            if (!empty($application->application_no)) {
                $requestData['application_no'] = $application->application_no;
            }

            $requestData['applicant_date_niti'] = !empty($requestData['applicant_date_niti'])?$requestData['applicant_date_niti']:null;

            // map applicant_type_display → audit_type (fallback กรณี JS ไม่ทำงาน)
            // 3 = ใบรับรอง 17025 → audit_type 1, 4 = ภาคผนวก ก. → audit_type 2
            if( empty($requestData['audit_type']) && !empty($requestData['applicant_type_display']) ){
                $requestData['audit_type'] = ($requestData['applicant_type_display'] == 3) ? 1 : 2;
            }

            if( empty($application->config_evidencce) ){
                $requestData['config_evidencce']  = (count(HP::ConfigEvidence(3)) > 0)?json_encode(HP::ConfigEvidence(3), JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE):null;
            }

             //กรณีมี ID section5_labs id
            if( !empty( $requestData['lab_id'] ) ){
                $labs = Labs::where('id', $requestData['lab_id'] )->first();
                $requestData['lab_id']   = !empty($labs->id)?$labs->id:null;
                $requestData['lab_code'] = !empty($labs->lab_code)?$labs->lab_code:null;
            } else {
                // ไม่เขียนทับ lab_id เดิมที่มีอยู่แล้ว เพื่อป้องกัน lab_id กลายเป็น NULL
                if( !empty($application->lab_id) ){
                    unset($requestData['lab_id'], $requestData['lab_code']);
                } else {
                    $requestData['lab_id']   = null;
                    $requestData['lab_code'] = null;
                }
            }

            $application->update( $requestData );

            // Use the persisted record id instead of trusting a hidden form field.
            $LabAcceptData['application_lab_id']    = $application->id;
            $LabAcceptData['application_no']        = $application->application_no;
            $LabAcceptData['application_status']    = $requestData['application_status'];
            $LabAcceptData['description']           = !empty($requestData['edit_detail'])?$requestData['edit_detail']:null;
            $LabAcceptData['appointment_date']      = 'edit_page';
            $LabAcceptData['created_by']            = auth()->user()->getKey();
            $LabAcceptData['created_at']            = date('Y-m-d H:i:s');

            ApplicationLabAccept::create($LabAcceptData);

            $this->SaveScope( $application, $requestData  );

            $this->SaveAudit( $application, $requestData  );

            $this->SaveFile( $application, $request );

            DB::commit();
            return redirect('request-section-5/application-lab')->with('flash_message', 'เรียบร้อยแล้ว!');

        } catch (\Throwable $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            \Log::error('Section5 LAB applicant update failed', [
                'application_id' => $id,
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);
            return redirect()->back()
                ->withInput($request->except(['evidences', 'repeater-file-other', 'evidence_file_config', 'evidence_file_other']))
                ->with('message_error', 'เกิดข้อผิดพลาดในการบันทึกคำขอ กรุณาลองใหม่อีกครั้ง หากยังพบปัญหากรุณาแจ้งเจ้าหน้าที่');
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ApplicationLab::destroy($id);
        return redirect('request-section-5/application-lab')->with('flash_message_delete', 'ลบข้อมูลเรียบร้อยแล้ว!');
    }


    public function GetTestItem(Request $request, $tis_id)
    {
        $search = $request->get('q');

        if( !empty($tis_id) && is_numeric($tis_id) ){

            $query = TestItem::from('bsection5_test_item AS child')
                            ->leftJoin('bsection5_test_item AS parent', 'child.main_topic_id', '=', 'parent.id')
                            ->where('child.tis_id', $tis_id)
                            ->where(function($query){
                                $query->where('child.input_result', 1)->Orwhere('child.test_summary', 1);
                            })
                            ->when($search, function ($query, $search) {
                                return $query->where(function ($q) use ($search) {
                                    $q->where('child.title', 'LIKE', "%$search%")
                                      ->orWhere('child.no', 'LIKE', "%$search%");
                                });
                            })
                            ->select('child.id', 'child.title', 'child.no', 'child.type', 'parent.no as parent_no', 'parent.title as parent_title');

            $items = $query->get()->sortBy(function ($item) {
                return $this->buildTestItemNoSortKey($item->no);
            })->values();

            $list = [];
            foreach( $items AS $item ){
                $data = new stdClass;
                $data->id = $item->id;
                
                $parent_info = ' <em>(ภายใต้หัวข้อทดสอบ '.(  ( !empty( $item->parent_no )?$item->parent_no.' ' :null ).$item->parent_title ).')</em>';

                switch ($item->type) {
                    case "1":
                        $test_item_option = ( !empty( $item->no )?'<span> &#8226; </span> ข้อ '.$item->no.' ' :'' ).$item->title.$parent_info; 
                        break;
                    case "2":
                        $test_item_option = ( !empty( $item->no )?'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span> &#9675; </span> ข้อ '.$item->no.' ' :'' ).$item->title.$parent_info; 
                        break;
                    case "3":
                        $test_item_option = ( !empty( $item->no )?'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span> &#9675; </span> ข้อ '.$item->no.' ' :'' ).$item->title.$parent_info; 
                        break;
                    default:
                        $test_item_option = ( !empty( $item->no )?'<span> &#8226; </span> ข้อ '.$item->no.' ' :'' ).$item->title.$parent_info; 
                }
                
                $data->text = $test_item_option;
                $data->title = $test_item_option;
                $list[] =  $data;
            }

            return response()->json($list);

        }

    }


    public function GetTestItemTools($id)
    {

        if(  !empty($id) && is_numeric($id) ){

            $data = TestItemTools::with('test_tool')
                                    ->whereHas('test_tool', function ($query) {
                                        $query->whereNotNull('id');
                                    })
                                    ->where( function($query) use($id ) {
                                        $query->where('bsection5_test_item_id',  $id);
                                    })
                                    ->select('test_tools_id')
                                    ->groupBy('test_tools_id')
                                    ->get();
            foreach( $data AS $item ){
                $item->id = $item->test_tool->id;
                $item->title = $item->test_tool->title;
            }      

            return response()->json($data);

        }

    }

    public function GetTestItemToolsStd($id)
    {

        if(  !empty($id) && is_numeric($id) ){

            $data = TestItemTools::with('test_tool')
                                    ->whereHas('test_tool', function ($query) {
                                        $query->whereNotNull('id');
                                    })
                                    ->whereHas('test_item', function($query) use($id ) {
                                        $query->where('tis_id',  $id);
                                    })
                                    ->select('test_tools_id')
                                    ->groupBy('test_tools_id')
                                    ->get();
            foreach( $data AS $item ){
                $item->id = $item->test_tool->id;
                $item->title = $item->test_tool->title;
            }      

            return response()->json($data);

        }

    }


    /**
     * สร้าง sort key จากเลขข้อ (เช่น "4.1.1.2(1)") แบบ natural sort ทีละ segment (คั่นด้วย '.')
     * รองรับทั้งตัวเลขและส่วนต่อท้ายที่ไม่ใช่ตัวเลข (เช่น "(1)", "(ก)") โดยข้อที่ไม่มีส่วนต่อท้าย
     * จะเรียงมาก่อนข้อที่มีส่วนต่อท้ายเสมอ (เช่น "4.1.1.2" มาก่อน "4.1.1.2(1)")
     */
    private function buildTestItemNoSortKey($no)
    {
        $segments = explode('.', (string) $no);
        $key = '';

        foreach ($segments as $segment) {
            preg_match('/^(\d*)(.*)$/u', trim($segment), $m);
            $num    = $m[1] !== '' ? $m[1] : '0';
            $suffix = $m[2];
            $key .= str_pad($num, 6, '0', STR_PAD_LEFT) . '|' . $suffix . '~';
        }

        return $key;
    }

    public function GetAllItemsWithTools(Request $request, $tis_id)
    {
        if (!empty($tis_id) && is_numeric($tis_id)) {

            $items = TestItem::where('tis_id', $tis_id)
                             ->where(function ($q) {
                                 $q->where('input_result', 1)->orWhere('test_summary', 1);
                             })
                             ->get(['id', 'no', 'title', 'type', 'tis_tisno', 'main_topic_id', 'parent_id', 'test_method_id']);

            // ข้อหัวข้อแม่ ไว้แสดงในกรณีรายการย่อยไม่มีเลขข้อของตัวเอง (no ว่างใน DB)
            $parent_map = TestItem::whereIn('id', $items->pluck('parent_id')->filter()->unique()->values())->get(['id', 'no', 'title'])->keyBy('id');

            $items = $items->sortBy(function ($item) {
                return $this->buildTestItemNoSortKey($item->no);
            })->values();

            // หน้า labs/show ขอ ?with_headings=1 เพื่อแสดงเป็นต้นไม้ข้อแม่/ข้อลูก — ดึงหัวข้อแม่ทุกชั้นที่ไม่อยู่ในรายการ (เช่น ข้อ 3., 4.) มาเป็นแถวหัวข้อ (selectable=false)
            $heading_rows = collect();
            if ($request->get('with_headings')) {
                $have_ids = $items->pluck('id')->all();
                $need = $items->pluck('parent_id')->filter()->unique()->diff($have_ids)->values();
                $guard = 0;
                while ($need->isNotEmpty() && $guard++ < 10) {
                    $anc = TestItem::where('tis_id', $tis_id)->whereIn('id', $need)->get(['id', 'no', 'title', 'type', 'tis_tisno', 'main_topic_id', 'parent_id', 'test_method_id']);
                    if ($anc->isEmpty()) break;
                    $heading_rows = $heading_rows->merge($anc);
                    $have_ids = array_merge($have_ids, $anc->pluck('id')->all());
                    $need = $anc->pluck('parent_id')->filter()->unique()->diff($have_ids)->values();
                }
            }

            $result = [];
            foreach ($items as $item) {
                $tools = TestItemTools::with('test_tool')
                                      ->whereHas('test_tool', function ($q) { $q->whereNotNull('id'); })
                                      ->where('bsection5_test_item_id', $item->id)
                                      ->select('test_tools_id')
                                      ->groupBy('test_tools_id')
                                      ->get()
                                      ->filter(function ($t) { return !is_null($t->test_tool); })
                                      ->map(function ($t) { return ['id' => $t->test_tool->id, 'title' => $t->test_tool->title]; })
                                      ->values();

                $result[] = [
                    'id'             => $item->id,
                    'no'             => $item->no,
                    'title'          => $item->title,
                    'type'           => $item->type,
                    'level'          => substr_count((string) $item->no, '.'),
                    'tis_tisno'      => $item->tis_tisno,
                    'main_topic_id'  => $item->main_topic_id,
                    'parent_id'      => $item->parent_id,
                    'parent_no'      => optional($parent_map->get($item->parent_id))->no,
                    'parent_title'   => optional($parent_map->get($item->parent_id))->title,
                    'test_method_id' => $item->test_method_id,
                    'tools'          => $tools->toArray(),
                ];
            }

            foreach ($heading_rows as $hd) {
                $result[] = [
                    'id'             => $hd->id,
                    'no'             => $hd->no,
                    'title'          => $hd->title,
                    'type'           => $hd->type,
                    'level'          => substr_count((string) $hd->no, '.'),
                    'tis_tisno'      => $hd->tis_tisno,
                    'main_topic_id'  => $hd->main_topic_id,
                    'parent_id'      => $hd->parent_id,
                    'parent_no'      => null,
                    'parent_title'   => null,
                    'test_method_id' => $hd->test_method_id,
                    'selectable'     => false,
                    'tools'          => [],
                ];
            }

            return response()->json($result);
        }
        return response()->json([]);
    }

    public function GetTisName($id)
    {
        $data = Tis::find($id);

        return response()->json($data);
    }

    public function AutoRunRefApplication()
    {
        $today = date('Y-m-d');
        $dates = explode('-', $today);
        $year = ( date('y')  + 43);
        $ref = 'LAB';

        $query_check = ApplicationLab::select('application_no')->whereYear('created_at',$dates[0])->orderBy('application_no')->get();
        $query = 0;
        if(count($query_check) != 0){

            $last_data = $query_check->last();
            if(!empty($last_data->application_no)){
                $application_no =  $last_data->application_no;
                $cut = explode('-', $application_no);
                $query = (int)($cut[2]);
            }

            $Seq = substr("000".((string)$query + 1),-4,4);
            $strNextSeq = $ref.'-'.$year ."-".$Seq;

            $no_check = ApplicationLab::where('application_no', $strNextSeq )->first();
            if(is_null($no_check)){
                return $strNextSeq;
            }else{
                $Seq = substr("000".((string)$query + 2),-4,4);
                $strNextSeq = $ref.'-'.$year ."-".$Seq;
                return $strNextSeq;
            }
        }else{
            $Seq = substr("000".((string)$query + 1),-4,4);
            $strNextSeq = $ref.'-'.$year ."-".$Seq;
            return $strNextSeq;
        }

    }

    public function data_list_cer(Request $request)
    {

        $tax_id =  $request->get('tax_id');
        $filter_search =  $request->get('filter_search');


        $query = CertificateExport::query()->when( $filter_search , function ($query, $filter_search){
                                                $search_full = str_replace(' ', '', $filter_search);
                                                return  $query->where(function ($query2) use($search_full) {

                                                    $ids = CertiLab::Where(DB::raw("REPLACE(lab_name,' ','')"), 'LIKE', "%".$search_full."%")->select('id');
                                                    $query2->Where(DB::raw("REPLACE(certificate_no,' ','')"), 'LIKE', "%".$search_full."%")
                                                            ->orWhereIn('certificate_for',  $ids  );
                                                    //         ->OrWhere(DB::raw("REPLACE(applicant_taxid,' ','')"), 'LIKE', "%".$search_full."%");
                                                });
                                            })
                                            ->where(function($query) use( $tax_id ){
                                                $ids = CertiLab::where('tax_id', $tax_id )->select('id');
                                                $query->whereIN('certificate_for',  $ids  );
                                            })
                                            ->whereHas('certificate_lab_export_mapreq', function ($query)  {
                                                   $query->whereNotNull('app_certi_lab_id' );
                                             });

        $DT    = Datatables::of($query);
        $DT->addIndexColumn();     
        $DT->addColumn('lab_name', function ($item) {
              $CertiLabTo = $item->CertiLabTo;
            return !is_null($CertiLabTo)?$CertiLabTo->lab_name:null;
             })
             ->addColumn('certificate_no', function ($item) {
                 return !is_null($item->certificate_no)?$item->certificate_no:null;
             })
             ->addColumn('accereditatio_no', function ($item) {
                 return !is_null($item->accereditatio_no)?$item->accereditatio_no:null;
             }); 
     $DT->addColumn('certificate_date_start', function ($item) {
                return !empty($item->CertiLabFileAll->start_date)?HP::revertDate($item->CertiLabFileAll->start_date):null;
            })
            ->addColumn('certificate_date_end', function ($item) {
                return !empty($item->CertiLabFileAll->end_date)?HP::revertDate($item->CertiLabFileAll->end_date):null;
            })
            ->addColumn('status', function ($item) {
                $certificate_date_end = !empty($item->CertiLabFileAll->end_date)?$item->CertiLabFileAll->end_date:null;
                if( $certificate_date_end >= date('Y-m-d') ){
                    return 'ใช้งาน';
                }else{
                    return 'หมดอายุ';
                }
            })
            ->addColumn('action', function ($item) {
                $certificate_date_end = !empty($item->CertiLabFileAll->end_date)?$item->CertiLabFileAll->end_date:null;
                if( $certificate_date_end >= date('Y-m-d') ){
                    return '<button class="btn btn-info btn_select_cer" type="button" data-accereditatio_no="'.($item->accereditatio_no).'" data-id="'.($item->id).'" data-table="'.((new CertificateExport)->getTable() ).'" data-certificate_no="'.(!is_null($item->certificate_no)?$item->certificate_no:null).'" data-date_end="'.( !empty($item->CertiLabFileAll->end_date)?HP::revertDate($item->CertiLabFileAll->end_date):null ).'" data-date_start="'.( !empty($item->CertiLabFileAll->start_date)?HP::revertDate($item->CertiLabFileAll->start_date):null ).'"> เลือก </button>';
                }else{
                    return '<button class="btn btn-info" type="button" disabled> เลือก </button>';
                }

            });
   return $DT->rawColumns([ 'action'])
              ->make(true);

 

        // $query = CertificateExport::query()->when( $filter_search , function ($query, $filter_search){
        //                                         $search_full = str_replace(' ', '', $filter_search);
        //                                         return  $query->where(function ($query2) use($search_full) {

        //                                             $ids = CertiLab::Where(DB::raw("REPLACE(lab_name,' ','')"), 'LIKE', "%".$search_full."%")->select('id');
        //                                             $query2->Where(DB::raw("REPLACE(certificate_no,' ','')"), 'LIKE', "%".$search_full."%")
        //                                                     ->orWhereIn('certificate_for',  $ids  );
        //                                             //         ->OrWhere(DB::raw("REPLACE(applicant_taxid,' ','')"), 'LIKE', "%".$search_full."%");
        //                                         });
        //                                     })
        //                                     ->where(function($query) use( $tax_id ){
        //                                         $ids = CertiLab::where('tax_id', $tax_id )->select('id');
        //                                         $query->whereIN('certificate_for',  $ids  );
        //                                     })
        //                                     ->whereHas('cert_labs_file_all', function ($query)  {
        //                                         $query->where('state',  1  );
        //                                     });
        // return Datatables::of($query)
        //                     ->addIndexColumn()
        //                     ->addColumn('lab_name', function ($item) {
        //                         $CertiLabTo = $item->CertiLabTo;
        //                         return !is_null($CertiLabTo)?$CertiLabTo->lab_name:null;
        //                     })
        //                     ->addColumn('certificate_no', function ($item) {
        //                         return !is_null($item->certificate_no)?$item->certificate_no:null;
        //                     })
        //                     ->addColumn('accereditatio_no', function ($item) {
        //                         return !is_null($item->accereditatio_no)?$item->accereditatio_no:null;
        //                     })
        //                     ->addColumn('certificate_date_start', function ($item) {

        //                         $cert_labs_file_all =  $item->cert_labs_file_all()->where('state', 1)->get()->last();
        //                         return !empty($cert_labs_file_all->start_date)?HP::revertDate($cert_labs_file_all->start_date):null;
        //                     })
        //                     ->addColumn('certificate_date_end', function ($item) {
        //                         $cert_labs_file_all =  $item->cert_labs_file_all()->where('state', 1)->get()->last();
        //                         return !empty($cert_labs_file_all->end_date)?HP::revertDate($cert_labs_file_all->end_date):null;
        //                     })
        //                     ->addColumn('status', function ($item) {
        //                         $cert_labs_file_all =  $item->cert_labs_file_all()->where('state', 1)->get()->last();
        //                         $certificate_date_end = !empty($cert_labs_file_all->end_date)?$cert_labs_file_all->end_date:null;
        //                         if( $certificate_date_end >= date('Y-m-d') ){
        //                             return 'ใช้งาน';
        //                         }else{
        //                             return 'หมดอายุ';
        //                         }
        //                     })
        //                     ->addColumn('action', function ($item) {
        //                         $cert_labs_file_all =  $item->cert_labs_file_all()->where('state', 1)->get()->last();
        //                         $certificate_date_end = !is_null($cert_labs_file_all->end_date)?$cert_labs_file_all->end_date:null;
        //                         if( $certificate_date_end >= date('Y-m-d') ){
        //                             return '<button class="btn btn-info btn_select_cer" type="button" data-accereditatio_no="'.($item->accereditatio_no).'" data-id="'.($item->id).'" data-table="'.((new CertificateExport)->getTable() ).'" data-certificate_no="'.(!is_null($item->certificate_no)?$item->certificate_no:null).'" data-date_end="'.( !empty($cert_labs_file_all->end_date)?HP::revertDate($cert_labs_file_all->end_date):null ).'" data-date_start="'.( !empty($cert_labs_file_all->start_date)?HP::revertDate($cert_labs_file_all->start_date):null ).'"> เลือก </button>';
        //                         }else{
        //                             return '<button class="btn btn-info" type="button" disabled> เลือก </button>';
        //                         }

        //                     })
        //                     ->rawColumns(['checkbox', 'action'])
        //                     ->make(true);
    }

    public function save_test_tools(Request $request)
    {

        $test_item =  $request->get('test_item');
        $test_tool =  $request->get('test_tool');
        $test_tool_id =  $request->get('test_tool_id');
        $type =  $request->get('type');

        if( $type == 1){
            $check = TestTool::where(DB::raw("REPLACE(title,' ','')"), $test_tool )->first();
        }else{
            $check = TestTool::where( 'id', $test_tool_id )->first();
        }

        $tools_id = null;

        if( !is_null($test_item) ){

            if( is_null($check) ){

                $newtools['title'] = $test_tool;
                $newtools['state'] = 1;
                $newtools['created_by'] = 0;

                $tools = TestTool::create($newtools);
                $tools_id = $tools->id;
                $item_tools = TestItemTools::Where('bsection5_test_item_id', $test_item )->where( 'test_tools_id', $tools->id  )->first();

                if( is_null($item_tools) ){

                    $toolsT = new TestItemTools;
                    $toolsT->bsection5_test_item_id = $test_item;
                    $toolsT->test_tools_id = $tools->id;
                    $toolsT->save();
                }

                $mgs = 'success';

            }else{

                $tools = $check;
                $tools_id = $tools->id;
                $item_tools = TestItemTools::Where('bsection5_test_item_id', $test_item )->where( 'test_tools_id', $tools->id  )->first();

                if( is_null($item_tools) ){

                    $toolsT = new TestItemTools;
                    $toolsT->bsection5_test_item_id = $test_item;
                    $toolsT->test_tools_id = $tools->id;
                    $toolsT->save();

                }

                $mgs = 'success';
            }

        }else{
            $mgs = "not success";
        }

        $data = new stdClass;
        $data->mgs = $mgs;
        $data->tools_id = $tools_id;

        return response()->json($data);


    }

    public function GetBasicTools($test_item_id)
    {
        $data = TestTool::where(function($query) use($test_item_id){
                                $ids = DB::table((new TestItemTools)->getTable().' AS item')
                                            ->leftJoin((new TestTool)->getTable().' AS tools', 'tools.id', '=', 'item.test_tools_id')
                                            ->where( function($query) use($test_item_id ) {
                                                $query->where('item.bsection5_test_item_id',  $test_item_id);
                                            })
                                            ->select('tools.id');

                                $query->whereNotIn('id',  $ids);
                            })
                            ->select('title', 'id')
                            ->get();

        return response()->json($data);

    }

    public function delete_update(Request $request)
    {
        try {

            $id = $request->get('id');
            $application = ApplicationLab::findOrFail($id);

            $requestData = $request->all();
            $requestData['application_status'] = 100;
            $requestData['delete_by'] = auth()->user()->getKey();
            $requestData['delete_at'] = date('Y-m-d h:i:s');
            $requestData['delete_state'] = 1;

            $application->update($requestData);

            if( $application ){
                return 'success';
            }else{
                return 'error';
            }

        } catch (\Exception $e) {

            return 'error';

        }
    }

    public function manage_scope(Request $request) {
        $item_id = $request->get('item_id');
        $applicationLab = null;
        if ($item_id) {
            $applicationLab = ApplicationLab::find($item_id);
        }
        return view(
            'section5.application-lab.manage-scope',
            compact('applicationLab')
        );
    }

    public function submit_scope_change(Request $request)
    {
        try {
            $labs = \App\Models\Section5\Labs::findOrFail($request->lab_id);
            abort_unless($this->ownsLab($labs), 403, 'คุณไม่มีสิทธิ์ยื่นคำขอให้หน่วยตรวจสอบ (LAB) นี้');
            $type = $request->type; // 2 = Add, 3 = Minus

            // Find existing Draft application (status 1) or create a new one
            $application = ApplicationLab::where('lab_id', $labs->id)
                                         ->where('application_status', 1)
                                         ->first();

            if (!$application) {
                // Determine `applicant_type`. If it's a mix later, it might be 4. For now, set to the requested type.
                $application = new ApplicationLab;
                $application->application_date = date('Y-m-d');
                $application->applicant_type = $type; // 2 or 3
                $application->application_status = 1; // 1 = Draft
                
                // Map the data from Labs
                $application->lab_id = $labs->id;
                $application->lab_code = $labs->lab_code;
                $application->lab_name = $labs->lab_name;
                $application->applicant_name = $labs->name;
                $application->applicant_taxid = $labs->taxid;
                
                $application->hq_address = $labs->lab_address;
                $application->hq_moo = $labs->lab_moo;
                $application->hq_soi = $labs->lab_soi;
                $application->hq_road = $labs->lab_road;
                $application->hq_building = $labs->lab_building;
                $application->hq_subdistrict_id = $labs->lab_subdistrict_id;
                $application->hq_district_id = $labs->lab_district_id;
                $application->hq_province_id = $labs->lab_province_id;
                $application->hq_zipcode = $labs->lab_zipcode;
                
                $application->lab_address = $labs->lab_address;
                $application->lab_moo = $labs->lab_moo;
                $application->lab_soi = $labs->lab_soi;
                $application->lab_road = $labs->lab_road;
                $application->lab_building = $labs->lab_building;
                $application->lab_subdistrict_id = $labs->lab_subdistrict_id;
                $application->lab_district_id = $labs->lab_district_id;
                $application->lab_province_id = $labs->lab_province_id;
                $application->lab_zipcode = $labs->lab_zipcode;
                $application->lab_phone = $labs->lab_phone;
                $application->lab_fax = $labs->lab_fax;
                
                $application->co_name = $labs->co_name;
                $application->co_position = $labs->co_position;
                $application->co_mobile = $labs->co_mobile;
                $application->co_phone = $labs->co_phone;
                $application->co_fax = $labs->co_fax;
                $application->co_email = $labs->co_email;
                
                $application->created_by = auth()->user()->getKey();
                $application->save();
            } else {
                // If appending to an existing draft, we might change applicant_type to 4 (แก้ไขข้อมูล) if mixed
                if ($application->applicant_type != $type && in_array($application->applicant_type, [2, 3])) {
                    $application->applicant_type = 4; // Mix of add/minus means it's a general modification
                    $application->save();
                }
            }

            // Save scopes
            if($type == 2){ // Add Scope
                 $repeater = $request->get('repeater-scope');
                 if(!empty($repeater)){
                     foreach($repeater as $item) {
                         $scope = new \App\Models\Section5\ApplicationLabScope;
                         $scope->application_lab_id = $application->id;
                         $scope->lab_id = $labs->id;
                         $scope->lab_code = $labs->lab_code;
                         $scope->tis_id = $item['tis_id'] ?? null;
                         $scope->tis_tisno = $item['tis_tisno'] ?? null;
                         $scope->test_item_id = $item['test_item_id'] ?? null;
                         $scope->test_tools_no = $item['test_tools_no'] ?? null;
                         $scope->capacity = $item['capacity'] ?? null;
                         $scope->range = $item['range'] ?? null;
                         $scope->true_value = $item['true_value'] ?? null;
                         $scope->fault_value = $item['fault_value'] ?? null;
                         $scope->test_duration = $item['test_duration'] ?? null;
                         $scope->test_price = $item['test_price'] ?? null;
                         $scope->type = 2; // Store explicit type on scope
                         $scope->save();
                     }
                 }
            } else if($type == 3){ // Reduce Scope
                 $scope_id = $request->get('scope_id');
                 if(!empty($scope_id)){
                      $lab_scopes = \App\Models\Section5\LabsScope::whereIn('id', $scope_id)->get();
                      foreach($lab_scopes as $lab_scope) {
                         // Check if this reduction is already in draft
                         $exists = \App\Models\Section5\ApplicationLabScope::where('application_lab_id', $application->id)
                                     ->where('test_item_id', $lab_scope->test_item_id)
                                     ->where('type', 3)
                                     ->first();
                         if (!$exists) {
                             $scope = new \App\Models\Section5\ApplicationLabScope;
                             $scope->application_lab_id = $application->id;
                             $scope->lab_id = $labs->id;
                             $scope->lab_code = $labs->lab_code;
                             $scope->tis_id = $lab_scope->tis_id;
                             $scope->tis_tisno = $lab_scope->tis_tisno;
                             $scope->test_item_id = $lab_scope->test_item_id;
                             $scope->remarks_reduce = $request->get('mn_close_remarks');
                             $scope->type = 3;
                             $scope->live_scope_id = $lab_scope->id;
                             $scope->save();
                         }
                      }
                 }
            }

        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e; // 403 จาก ownsLab() ต้องไม่ถูกกลืนเป็น JSON error ปกติ
            }
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function final_submit_scope(Request $request)
    {
        try {
            $labs = \App\Models\Section5\Labs::findOrFail($request->lab_id);
            abort_unless($this->ownsLab($labs), 403, 'คุณไม่มีสิทธิ์ยื่นคำขอให้หน่วยตรวจสอบ (LAB) นี้');

            $add_scope_ids    = $request->input('add_scope_tis_id', []);
            $minus_scope_ids  = $request->input('minus_scope_id', []);

            $has_add   = !empty(array_filter((array)$add_scope_ids));
            $has_minus = !empty(array_filter((array)$minus_scope_ids));

            if (!$has_add && !$has_minus) {
                return redirect('request-section-5/labs/show/'.$labs->id)->with('message_error', 'กรุณาเพิ่มรายการขอเพิ่ม/ลดขอบข่ายอย่างน้อย 1 รายการ');
            }

            // ห้ามยื่นเพิ่ม+ลดขอบข่ายพร้อมกันในคำขอเดียว ต้องแยกยื่นคนละคำขอ (เดิม applicant_type=4 "ผสม"
            // อนุญาตให้ยื่นพร้อมกันได้ แต่ทำให้ตรวจสอบ/อนุมัติซับซ้อนขึ้นและไม่ตรงกับ workflow จริง — บล็อกให้
            // ตรงกับฝั่ง IBCB ที่ final_submit_scope() บล็อกเงื่อนไขนี้อยู่แล้ว)
            if ($has_add && $has_minus) {
                return redirect('request-section-5/labs/show/'.$labs->id)->with('message_error', 'กรุณายื่นคำขอเพิ่มขอบข่าย และคำขอลดขอบข่าย แยกคำขอกัน ไม่สามารถยื่นรวมกันในคำขอเดียวได้');
            }

            // ตรวจแถวขอเพิ่มฝั่ง server (client เช็คแล้ว แต่ห้ามเชื่อ) — ต้องกรอกรายละเอียดครบ และห้ามยื่นเครื่องมือ/รายการที่ได้รับแล้วซ้ำ
            if ($has_add) {
                $add_error = $this->validateAddScopeRows($labs, $request);
                if (!empty($add_error)) {
                    return redirect('request-section-5/labs/show/'.$labs->id.(!empty($request->input('application_id')) ? '?application_id='.$request->input('application_id') : ''))
                                ->with('message_error', $add_error);
                }
            }

            // Determine applicant_type: 2=เพิ่ม, 3=ลด
            $applicant_type = $has_add ? 2 : 3;

            // อัปเดตคำขอเดิมเฉพาะตอนฟอร์มส่ง application_id มาชัดเจน (มาจากปุ่ม "แก้ไข" ที่เจาะจงคำขอนั้น
            // ผ่าน ApplicationLabController::edit() เท่านั้น) — ถ้าไม่มี application_id (กด "เพิ่ม/ลดขอบข่าย"
            // ใหม่ตรงๆ จากหน้า labs/show) ต้องได้คำขอใหม่เสมอ แม้จะมีคำขอเก่าที่ถูกตีกลับค้างอยู่ก็ตาม —
            // เดิม auto-guess "ฉบับร่างล่าสุด" เอง ทำให้ยื่นใหม่ไปทับคำขอเก่าโดยไม่ตั้งใจ
            // (มิเรอร์บั๊กเดียวกับที่แก้ใน IBCB final_submit_scope())
            $application = null;
            $requested_application_id = $request->input('application_id');
            if (!empty($requested_application_id)) {
                $application = ApplicationLab::where('id', $requested_application_id)
                                             ->where('lab_id', $labs->id)
                                             ->whereIn('applicant_type', [2, 3, 4])
                                             ->whereIn('application_status', [0, 2, 15, 16])
                                             ->first();
            }

            // Never silently create a new request when the edit form identified
            // an existing request that is no longer editable or cannot be found.
            if (!empty($requested_application_id) && !$application) {
                return redirect()->back()->with('message_error', 'ไม่พบคำขอเดิมหรือคำขอนี้ไม่อยู่ในสถานะที่แก้ไขได้ กรุณาตรวจสอบอีกครั้ง');
            }

            DB::beginTransaction();

            if (!$application) {
                $application = new ApplicationLab;
                $application->application_date  = date('Y-m-d');
                $application->application_status = 0;
                $application->lab_id            = $labs->id;
                $application->lab_code          = $labs->lab_code;
                $application->lab_name          = $labs->lab_name;
                $application->applicant_name    = $labs->name;
                $application->applicant_taxid   = $labs->taxid;

                $application->hq_address        = $labs->lab_address;
                $application->hq_moo            = $labs->lab_moo;
                $application->hq_soi            = $labs->lab_soi;
                $application->hq_road           = $labs->lab_road;
                $application->hq_building       = $labs->lab_building;
                $application->hq_subdistrict_id = $labs->lab_subdistrict_id;
                $application->hq_district_id    = $labs->lab_district_id;
                $application->hq_province_id    = $labs->lab_province_id;
                $application->hq_zipcode        = $labs->lab_zipcode;

                $application->lab_address       = $labs->lab_address;
                $application->lab_moo           = $labs->lab_moo;
                $application->lab_soi           = $labs->lab_soi;
                $application->lab_road          = $labs->lab_road;
                $application->lab_building      = $labs->lab_building;
                $application->lab_subdistrict_id= $labs->lab_subdistrict_id;
                $application->lab_district_id   = $labs->lab_district_id;
                $application->lab_province_id   = $labs->lab_province_id;
                $application->lab_zipcode       = $labs->lab_zipcode;
                $application->lab_phone         = $labs->lab_phone;
                $application->lab_fax           = $labs->lab_fax;

                $application->co_name           = $labs->co_name;
                $application->co_position       = $labs->co_position;
                $application->co_mobile         = $labs->co_mobile;
                $application->co_phone          = $labs->co_phone;
                $application->co_fax            = $labs->co_fax;
                $application->co_email          = $labs->co_email;

                $application->created_by        = auth()->user()->getKey();
            }

            // Update applicant_type on existing draft too
            $application->applicant_type = $applicant_type;
            $application->audit_type = $request->input('audit_type', 1);
            $application->updated_by = auth()->user()->getKey();

            // ─── Generate application_no (เฉพาะคำขอใหม่ที่ยังไม่มีเลข ไม่งั้นคำขอเดิมจะได้เลขใหม่ทุกครั้งที่แก้ไข) ──
            if (empty($application->application_no)) {
                $running_no = HP::ConfigFormat('APP-LAB', (new ApplicationLab)->getTable(), 'application_no', null, null, null);
                $application_check = ApplicationLab::where('application_no', $running_no)->first();
                if (!is_null($application_check)) {
                    $running_no = HP::ConfigFormat('APP-LAB', (new ApplicationLab)->getTable(), 'application_no', null, null, null);
                }
                $application->application_no = $running_no;
            }
            // ส่งคำขอที่แก้ไขแล้วกลับเข้าคิวตรวจสอบปกติ
            $application->application_status = 1;
            $application->save();

            // ─── บันทึกใบรับรองระบบงานตามฐาน 17025 / ภาคผนวก ก. ─────────────
            $this->SaveAudit($application, $request->all());

            // ─── CLEAR EXISTING SCOPES (Clean Slate for Submitted Application) ───
            \App\Models\Section5\ApplicationLabScope::where('application_lab_id', $application->id)->delete();

            // ─── Save Add Scopes (type=2) ───────────────────────────────────
            if ($has_add) {
                $add_tis_ids      = $request->input('add_scope_tis_id', []);
                $add_tis_tisnos   = $request->input('add_scope_tis_tisno', []);
                $add_test_items   = $request->input('add_scope_test_item_id', []);
                $add_tools_ids    = $request->input('add_scope_test_tools_id', []);
                $add_tools_custom_names = $request->input('add_scope_test_tools_custom_name', []);
                $add_tools_nos    = $request->input('add_scope_test_tools_no', []);
                $add_capacities   = $request->input('add_scope_capacity', []);
                $add_ranges       = $request->input('add_scope_range', []);
                $add_true_values  = $request->input('add_scope_true_value', []);
                $add_fault_values = $request->input('add_scope_fault_value', []);
                $add_test_durations = $request->input('add_scope_test_duration', []);
                $add_test_prices  = $request->input('add_scope_test_price', []);
                $add_price_sets   = $request->input('add_scope_test_price_set', []);
                $add_method_types = $request->input('add_scope_test_method_type', []);
                $add_method_others = $request->input('add_scope_test_method_other', []);
                $add_lab_remarks  = $request->input('add_scope_lab_remark', []);

                foreach ($add_tis_ids as $idx => $tis_id) {
                    if (empty($tis_id)) continue;

                    $test_tools_id = !empty($add_tools_ids[$idx]) ? $add_tools_ids[$idx] : null;
                    if (is_null($test_tools_id) && !empty($add_tools_custom_names[$idx])) {
                        $customName = trim($add_tools_custom_names[$idx]);
                        $existTool  = TestTool::where(DB::raw("REPLACE(title,' ','')"), str_replace(' ', '', $customName))->first();
                        if (is_null($existTool)) {
                            $existTool = TestTool::create(['title' => $customName, 'state' => 1, 'created_by' => 0]);
                        }
                        $test_tools_id = $existTool->id;
                        TestItemTools::firstOrCreate([
                            'bsection5_test_item_id' => $add_test_items[$idx] ?? null,
                            'test_tools_id'          => $test_tools_id,
                        ]);
                    }

                    $scope = new \App\Models\Section5\ApplicationLabScope;
                    $scope->application_lab_id = $application->id;
                    $scope->application_no     = $application->application_no;
                    $scope->lab_id             = $labs->id;
                    $scope->lab_code           = $labs->lab_code;
                    $scope->tis_id             = $tis_id;
                    $scope->tis_tisno          = $add_tis_tisnos[$idx] ?? null;
                    $scope->test_item_id       = $add_test_items[$idx] ?? null;
                    $scope->test_tools_id      = $test_tools_id;
                    $scope->test_tools_no      = $add_tools_nos[$idx] ?? null;
                    $scope->capacity           = $add_capacities[$idx] ?? null;
                    $scope->range              = $add_ranges[$idx] ?? null;
                    $scope->true_value         = $add_true_values[$idx] ?? null;
                    $scope->fault_value        = $add_fault_values[$idx] ?? null;
                    $scope->test_duration      = $add_test_durations[$idx] ?? null;
                    $scope->test_price         = $add_test_prices[$idx] ?? null;
                    $scope->test_price_per_set = !empty($add_price_sets[$idx]) ? trim($add_price_sets[$idx]) : null;
                    $scope->test_method_type   = !empty($add_method_types[$idx]) ? (int) $add_method_types[$idx] : null;
                    $scope->test_method_other  = (in_array((int) ($add_method_types[$idx] ?? 0), [2, 3], true) && !empty($add_method_others[$idx])) ? trim($add_method_others[$idx]) : null;
                    $scope->lab_remark         = !empty($add_lab_remarks[$idx]) ? trim($add_lab_remarks[$idx]) : null;
                    $scope->type               = 2;
                    $scope->save();
                }
            }

            // ─── Save Minus Scopes (type=3) ─────────────────────────────────
            if ($has_minus) {
                $minus_tis_ids      = $request->input('minus_scope_tis_id', []);
                $minus_tis_tisnos   = $request->input('minus_scope_tis_tisno', []);
                $minus_test_items   = $request->input('minus_scope_test_item_id', []);
                $minus_remarks      = $request->input('minus_scope_remarks_reduce', []);
                $minus_live_ids     = $request->input('minus_scope_id', []);

                foreach ($minus_tis_ids as $idx => $tis_id) {
                    if (empty($tis_id)) continue;

                    $scope = new \App\Models\Section5\ApplicationLabScope;
                    $scope->application_lab_id = $application->id;
                    $scope->application_no     = $application->application_no;
                    $scope->lab_id             = $labs->id;
                    $scope->lab_code           = $labs->lab_code;
                    $scope->tis_id             = $tis_id;
                    $scope->tis_tisno          = $minus_tis_tisnos[$idx] ?? null;
                    $scope->test_item_id       = $minus_test_items[$idx] ?? null;
                    $scope->remarks_reduce     = $minus_remarks[$idx] ?? null;
                    $scope->live_scope_id      = $minus_live_ids[$idx] ?? null;
                    $scope->type               = 3;
                    $scope->save();
                }
            }

            // ─── Handle Attachments ─────────────────────────────────────────
            if ($request->hasFile('evidence_file_config')) {
                $this->attach_file_config($application, $request);
            }
            if (!empty($request->input('repeater-file-other'))) {
                $this->attach_files_other($application, $request);
            }

            // Keep the applicant resubmission history in sync with the main record.
            ApplicationLabAccept::create([
                'application_lab_id' => $application->id,
                'application_no'     => $application->application_no,
                'application_status' => $application->application_status,
                'description'        => $request->input('edit_detail'),
                'appointment_date'   => 'edit_page',
                'created_by'         => auth()->user()->getKey(),
                'created_at'         => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            return redirect('request-section-5/application-lab')->with('flash_message', 'ยื่นคำขอสำเร็จ ! เลขที่อ้างอิง: '.$application->application_no);

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e; // 403 จาก ownsLab() ต้องไม่ถูกกลืนเป็น flash error ปกติ
            }
            \Log::error('Section5 LAB scope submission failed', [
                'application_id' => $request->input('application_id'),
                'lab_id' => $request->input('lab_id'),
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);
            return redirect()->back()
                ->withInput($request->except(['evidence_file_config', 'evidence_file_other']))
                ->with('message_error', 'เกิดข้อผิดพลาดในการบันทึกคำขอ กรุณาลองใหม่อีกครั้ง หากยังพบปัญหากรุณาแจ้งเจ้าหน้าที่');
        }
    }

    /**
     * ตรวจแถว "ขอเพิ่มขอบข่าย" (add_scope_*[]) ก่อนบันทึก คืนข้อความ error หรือ null ถ้าผ่าน
     * - ขอเพิ่มเครื่องมือได้เฉพาะที่ยังไม่เคยได้รับ: (test_item, tool) ซ้ำกับที่ได้รับแล้ว (คำขอที่ประกาศราชกิจจาฯ แล้ว audit_result=1) หรือซ้ำกันเองในคำขอ = ห้าม
     * - รายการทดสอบที่ได้รับแล้วต้องมีเครื่องมือใหม่ (ห้ามยื่นรายการ/เครื่องมือเดิมซ้ำ)
     * - เครื่องมือที่ไม่ใช่ตรวจพินิจต้องกรอก ขีดความสามารถ/ช่วง/ความละเอียด/คลาดเคลื่อน/ระยะเวลา ให้ครบ (ค่าใช้จ่าย/ชุดละ ไม่บังคับ)
     * - แถวจากตารางหน้า labs/show (add_scope_item_fields=1) ต้องมี ราคาค่าทดสอบ/ต่อชุดตัวอย่าง และวิธีทดสอบของ LAB (ถ้าเลือก 2=เทียบเท่า หรือ 3=อื่นๆ ต้องระบุรายละเอียด)
     */
    private function validateAddScopeRows($labs, $request)
    {
        $tis_ids   = (array) $request->input('add_scope_tis_id', []);
        $items     = (array) $request->input('add_scope_test_item_id', []);
        $tool_ids  = (array) $request->input('add_scope_test_tools_id', []);
        $customs   = (array) $request->input('add_scope_test_tools_custom_name', []);
        $detail_keys = [
            'add_scope_capacity'      => 'ขีดความสามารถ',
            'add_scope_range'         => 'ช่วงการใช้งาน',
            'add_scope_true_value'    => 'ความละเอียดที่อ่านได้',
            'add_scope_fault_value'   => 'ความคลาดเคลื่อนที่ยอมรับ',
            'add_scope_test_duration' => 'ระยะการทดสอบ(วัน)',
        ];
        $item_fields   = (array) $request->input('add_scope_item_fields', []);
        $price_sets    = (array) $request->input('add_scope_test_price_set', []);
        $method_types  = (array) $request->input('add_scope_test_method_type', []);
        $method_others = (array) $request->input('add_scope_test_method_other', []);
        $details = [];
        foreach ($detail_keys as $key => $label) {
            $details[$key] = (array) $request->input($key, []);
        }

        $ref_nos = \App\Models\Section5\LabsScope::where('lab_id', $labs->id)->pluck('ref_lab_application_no')->filter()->unique()->values();
        $approved_app_ids = ApplicationLab::where(function ($q) use ($labs, $ref_nos) {
                                    $q->where('lab_id', $labs->id)->orWhereIn('application_no', $ref_nos);
                                })
                                ->where('application_status', 99)
                                ->pluck('id');
        $existing_pairs = \App\Models\Section5\ApplicationLabScope::whereIn('application_lab_id', $approved_app_ids)
                                ->where('audit_result', 1)
                                ->where(function ($q) { $q->whereNull('type')->orWhere('type', '!=', 3); })
                                ->whereNotNull('test_tools_id')
                                ->get(['test_item_id', 'test_tools_id'])
                                ->map(function ($r) { return $r->test_item_id.'|'.$r->test_tools_id; })
                                ->flip()->all();
        $appointed_items = \App\Models\Section5\LabsScope::where('lab_id', $labs->id)->where('state', 1)
                                ->get(['tis_id', 'test_item_id'])
                                ->map(function ($r) { return $r->tis_id.'|'.$r->test_item_id; })
                                ->flip()->all();

        // 1 คำขอ ยื่นเพิ่มขอบข่ายได้เพียง 1 มอก.
        $distinct_tis = collect($tis_ids)->filter()->unique()->values();
        if ($distinct_tis->count() > 1) {
            return 'คำขอนี้ยื่นเพิ่มขอบข่ายได้เพียง 1 มอก. ต่อ 1 คำขอ (พบ '.$distinct_tis->count().' มอก.) กรุณาแยกยื่นคนละคำขอ';
        }

        $seen = [];
        foreach ($tis_ids as $idx => $tis_id) {
            if (empty($tis_id)) continue;

            $item_id = $items[$idx] ?? null;
            if (empty($item_id)) {
                return 'ข้อมูลรายการทดสอบไม่ครบถ้วน กรุณาเลือกรายการทดสอบใหม่อีกครั้ง';
            }

            $tool_id = !empty($tool_ids[$idx]) ? $tool_ids[$idx] : null;
            $custom  = isset($customs[$idx]) ? trim($customs[$idx]) : '';
            if (is_null($tool_id) && $custom !== '') {
                $exist_tool = TestTool::where(DB::raw("REPLACE(title,' ','')"), str_replace(' ', '', $custom))->first();
                $tool_id = !empty($exist_tool) ? $exist_tool->id : null;
            }
            $has_tool = !is_null($tool_id) || $custom !== '';

            // ข้อมูลระดับรายการทดสอบ (ตารางหน้า labs/show)
            if (!empty($item_fields[$idx])) {
                $ilabel = 'รายการทดสอบ "'.(optional(TestItem::find($item_id))->title ?: $item_id).'"';
                if (!isset($price_sets[$idx]) || trim((string) $price_sets[$idx]) === '') {
                    return $ilabel.' กรุณากรอก ราคาค่าทดสอบ/ต่อชุดตัวอย่าง';
                }
                $mt = isset($method_types[$idx]) ? (int) $method_types[$idx] : 0;
                if (!in_array($mt, [1, 2, 3], true)) {
                    return $ilabel.' กรุณาเลือก วิธีทดสอบของ LAB';
                }
                if (in_array($mt, [2, 3], true) && (!isset($method_others[$idx]) || trim((string) $method_others[$idx]) === '')) {
                    return $ilabel.($mt === 2 ? ' กรุณาระบุรายละเอียดวิธีเทียบเท่า' : ' กรุณาระบุ วิธีทดสอบของ LAB (อื่นๆ)');
                }
            }

            $item_title = optional(TestItem::find($item_id))->title;
            $label = 'รายการทดสอบ "'.(!empty($item_title) ? $item_title : $item_id).'"';

            if (!$has_tool) {
                // ตรวจพินิจ (ไม่มีเครื่องมือ) — ถ้าได้รับแล้วห้ามยื่นซ้ำ
                if (isset($appointed_items[$tis_id.'|'.$item_id])) {
                    return $label.' ได้รับแต่งตั้งแล้ว ไม่สามารถยื่นซ้ำได้';
                }
            } else {
                if (!is_null($tool_id) && isset($existing_pairs[$item_id.'|'.$tool_id])) {
                    return $label.' มีเครื่องมือนี้ได้รับแต่งตั้งแล้ว ไม่สามารถยื่นเครื่องมือเดิมซ้ำได้ (ยื่นได้เฉพาะเครื่องมือที่เพิ่มใหม่)';
                }
                $pair_key = $item_id.'|'.(!is_null($tool_id) ? $tool_id : 'c:'.mb_strtolower(str_replace(' ', '', $custom)));
                if (isset($seen[$pair_key])) {
                    return $label.' มีเครื่องมือซ้ำกันในคำขอเดียวกัน กรุณาเลือกเครื่องมือละ 1 แถว';
                }
                $seen[$pair_key] = true;

                if ((string) $tool_id !== '4') { // 4 = ตรวจพินิจ ไม่ต้องกรอกรายละเอียด
                    foreach ($detail_keys as $key => $field_label) {
                        if (!isset($details[$key][$idx]) || trim((string) $details[$key][$idx]) === '') {
                            return $label.' กรุณากรอก '.$field_label.' ให้ครบ';
                        }
                    }
                }
            }
        }

        return null;
    }

    private function attach_file_config($application, $request){
        $files = $request->file('evidence_file_config');
        $setting_titles = $request->input('setting_title');
        $setting_ids = $request->input('setting_id');

        if (is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file) && $file->isValid()) {
                    $setting_title = $setting_titles[$key] ?? 'ไฟล์เอกสารแนบ';
                    $setting_id    = $setting_ids[$key] ?? null;

                    // ลบไฟล์เดิมของ setting_file_id เดียวกันก่อน (กันไม่ให้ attach_files สะสม record ซ้ำ
                    // ทุกครั้งที่แก้ไข/อัปโหลดไฟล์ใหม่ทับช่องเดิม)
                    \App\AttachFile::where('ref_table', (new ApplicationLab)->getTable())
                        ->where('ref_id', $application->id)
                        ->where('section', 'evidence_file_config')
                        ->where('setting_file_id', $setting_id)
                        ->delete();

                    // ใช้ HP::singleFileUpload() แทนการเรียก Storage::putFileAs() ตรงๆ ด้วยชื่อไฟล์เดิม
                    // (ชื่อไฟล์ภาษาไทยจะถูก ext ftp ของ PHP เขียนเพี้ยนบน NAS หาไม่เจอ)
                    HP::singleFileUpload(
                        $file,
                        $this->attach_path.'/Section5/ApplicationLab/'.$application->application_no,
                        (auth()->user()->tax_number ?? null),
                        (auth()->user()->username ?? null),
                        'SSO',
                        (new ApplicationLab)->getTable(),
                        $application->id,
                        'evidence_file_config',
                        $setting_title,
                        $setting_id
                    );
                }
            }
        }
    }

    private function attach_files_other($application, $request){
        // ช่อง "เอกสารเพิ่มเติม" เป็น jquery.repeater (data-repeater-list="repeater-file-other")
        // ตัว plugin จะ rewrite name จริงใน DOM จาก "evidence_file_other"/"file_documents" เดี่ยวๆ
        // ให้กลายเป็น repeater-file-other[N][evidence_file_other] / repeater-file-other[N][file_documents]
        // ตอน init เสมอ (เหมือน pattern เดียวกับ evidences[] ใน SaveFile() ของ applicant_type=1)
        // ห้ามอ่านแบบ flat array ตรงๆ เพราะโครงสร้างจริงที่ส่งมาไม่ใช่แบบนั้น
        $repeater_file = $request->input('repeater-file-other', []);

        foreach ($repeater_file as $key => $item) {
            if ($request->hasFile("repeater-file-other.{$key}.evidence_file_other")) {
                $caption = !empty($item['file_documents']) ? $item['file_documents'] : 'เอกสารเพิ่มเติม';

                // เอกสารเพิ่มเติมเป็น repeater ตั้งใจให้แนบได้หลายไฟล์ ไม่ต้องลบของเดิมก่อน
                // ใช้ HP::singleFileUpload() แทน Storage::putFileAs() ตรงๆ (ดูเหตุผลที่ attach_file_config())
                HP::singleFileUpload(
                    $request->file("repeater-file-other.{$key}.evidence_file_other"),
                    $this->attach_path.'/Section5/ApplicationLab/'.$application->application_no,
                    (auth()->user()->tax_number ?? null),
                    (auth()->user()->username ?? null),
                    'SSO',
                    (new ApplicationLab)->getTable(),
                    $application->id,
                    'evidence_file_other',
                    $caption
                );
            }
        }
    }
    public function labs_show($id, Request $request)
    {
        $labs = \App\Models\Section5\Labs::findOrFail($id);
        abort_unless($this->ownsLab($labs), 403, 'คุณไม่มีสิทธิ์เข้าถึงหน่วยตรวจสอบ (LAB) นี้');
        $type = 'labs';

        // Get pending scopes from ApplicationLabScope
        // เดิมกรองแค่ application_status ไม่ได้กรอง applicant_type เลย ทำให้ดึง scope ของคำขอ "ยกเลิกการเป็น
        // หน่วยตรวจสอบ" (applicant_type=5) ติดมาด้วย — flow ยกเลิกสร้าง ApplicationLabScope โดยตั้ง type=3
        // เหมือนกับ "ขอลดขอบข่าย" เพื่อบอกว่าขอบข่ายนั้นจะถูกปิด ผลคือถ้า lab มีคำขอยกเลิกค้างพิจารณาอยู่
        // หน้า "จัดการ" ขอบข่ายจะเห็นแถว "ขอลดขอบข่าย (รอพิจารณา)" ทุกครั้งทั้งที่ไม่เกี่ยวกับขอบข่ายเลย
        // ดึงเฉพาะแถวของ "คำขอที่กำลังเปิดอยู่" (application_id) เท่านั้น — เดิมดึงของทุกคำขอที่ค้างอยู่ของ lab นี้รวมกัน
        // ทำให้เมื่อมีคำขอเพิ่มขอบข่ายค้างมากกว่า 1 ใบ (ข้อ/เครื่องมือเดียวกัน) แถวจะซ้ำกัน และถูกเติมลง panel/ส่งซ้ำเข้าคำขอนี้
        // คำขอใหม่ (ไม่มี application_id) เริ่มจากว่างเสมอ
        $pending_scopes = collect();
        $scope_app_id = $request->get('application_id');
        if (!empty($scope_app_id)) {
            $pending_scopes = \App\Models\Section5\ApplicationLabScope::where('lab_id', $labs->id)
                                ->where('application_lab_id', $scope_app_id)
                                ->whereHas('application_lab', function($query){
                                    $query->whereIn('applicant_type', [2, 3, 4]) // เฉพาะคำขอเพิ่ม/ลด/ผสมขอบข่าย
                                          ->whereIn('application_status', [0, 1, 2, 3, 4, 5, 7, 8, 15, 16]); // Pending statuses, including tool-fix requests
                                })
                                ->get();
        }

        // คำขอเดิมที่กำลังแก้ไขอยู่ — resolve เฉพาะตอนมี application_id ส่งมาชัดเจน (จากปุ่ม "แก้ไข"/"จัดการ"
        // ที่เจาะจงคำขอนั้นจริงๆ ผ่าน ApplicationLabController::edit()/show()) เท่านั้น ใช้ทั้ง pre-select
        // radio "ได้รับใบรับรองระบบงานตามฐาน 17025" และโชว์ประวัติ/หมายเหตุเจ้าหน้าที่
        // ห้าม auto-guess "ฉบับร่างล่าสุด" เอง เพราะกด "เพิ่ม/ลดขอบข่าย" ใหม่ตรงๆ จากหน้านี้ (ไม่ผ่าน edit())
        // ต้องได้ ID ใหม่เสมอ แม้จะมีคำขอเก่าที่ถูกตีกลับค้างอยู่ก็ตาม (ถ้า auto-guess จะไปทับของเก่าโดยไม่ตั้งใจ)
        $draft_app = null;
        $application_id = $request->get('application_id');
        if (!empty($application_id)) {
            $draft_app = \App\Models\Section5\ApplicationLab::where('id', $application_id)
                                ->where('lab_id', $labs->id)
                                ->whereIn('applicant_type', [2, 3, 4])
                                ->whereIn('application_status', [0, 2, 15, 16])
                                ->first();
        }

        return view('section5/labs.show', compact('labs', 'type', 'pending_scopes', 'draft_app'));
    }
}
