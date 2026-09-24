<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use HP;
use App\User;
use App\Models\Config\ConfigsEvidenceSystem;
use App\Models\Config\ConfigsEvidenceGroup;
use App\Models\Config\ConfigsEvidence;
use App\Models\Basic\BranchGroup;
use App\Models\Basic\Branch;
use App\Models\Basic\Subdistrict;
use App\Models\Basic\District;
use App\Models\Basic\Province;
use App\Models\Basic\BranchTis;
use App\Models\Basic\Tis;
use App\Models\Section5\Section5ApplicationInspector;
use App\Models\Section5\Section5ApplicationInspectorsScope;
use App\Models\Section5\Section5ApplicationInspectorsScopeTis;
use App\Models\Section5\ApplicationInspectorsAccept;


class ApplicationInspectorsController extends Controller
{

    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        set_time_limit(0);
        $this->middleware('auth');
        $this->attach_path = 'files/sso';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('section5/application_inspectors.index');
    }

    public function data_list(Request $request)
    {
        $filter_search =  $request->get('filter_search');
        $filter_branch =  $request->get('filter_branch');
        $filter_state =  $request->get('filter_state');
        $user_login = auth()->user();

        $query = Section5ApplicationInspector::query()->with([
                'sso_application_inspector_register_subs.basic_branches',
                'section5_application_inspectors_status'
            ])
            ->where(function($query) use($user_login){
                $query->where('applicant_taxid', $user_login->tax_number)->Orwhere('created_by', $user_login->id);
            })
            ->when($filter_search, function ($query, $filter_search) {
                $search_full = str_replace(' ', '', $filter_search);

                if (strpos($search_full, 'INS-') !== false) {
                    return $query->where('application_no',  'LIKE', "%$search_full%");
                } else {
                    return  $query->where(function ($query2) use ($search_full) {
                        $query2->Where(DB::raw("REPLACE(applicant_full_name,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->OrWhere(DB::raw("REPLACE(applicant_taxid,' ','')"), 'LIKE', "%" . $search_full . "%")
                            ->Orwhere('application_no',  'LIKE', "%$search_full%");
                    });
                }
            })
            ->when($filter_branch, function ($query, $filter_branch) {
                return $query->whereHas('sso_application_inspector_register_subs', function ($query) use ($filter_branch) {
                    $query->where('branch_group_id', '=', $filter_branch);
                });
            })
            ->when($filter_state, function ($query, $filter_state) {
                return $query->where('application_status', $filter_state);
            });
        return Datatables::of($query)
                            ->addIndexColumn()
                            ->addColumn('application_no', function ($item) {
                                return $item->application_no;
                            })
                            ->addColumn('applicant_full_name', function ($item) {
                                return $item->FullNameTaxId;
                            })
                            // ->addColumn('applicant_taxid', function ($item) {
                            //     return !empty($item->applicant_taxid)?$item->applicant_taxid:'-';
                            // })
                            ->addColumn('branch_id', function ($item) {
                                return $item->BranchGroupBranchName;
                            })
                            ->addColumn('application_date', function ($item) {
                                return !empty($item->application_date)?HP::DateThai($item->application_date):'-';
                            })
                            ->addColumn('application_status', function ($item) {
                                return !empty($item->AppStatus)?$item->AppStatus:'ฉบับร่าง';
                            })
                            ->addColumn('action', function ($item) {
                                $edit = true;
                                $delete = true;
                                $disabled = [];
                                if(!in_array($item->application_status, [3,12])){
                                    $edit = false;
                                    $delete = false;
                                    array_push($disabled, 'edit');
                                }

                                $created_at = !empty($item->created_at) ? HP::DateTimeThai($item->created_at) : null;
                                $button = HP::buttonAction($item->id, 'request_section5/application_inspectors', 'Section5\\ApplicationInspectorsController@destroy', 'application_inspectors', true, $edit, false, $disabled);
                                if($item->application_status != 11 && $item->application_status != 2 ){
                                    $button .= "<button type='button' class='btn btn-danger btn-xs btn_delete' title='Delete application_inspector'
                                                        data-id='{$item->id}'
                                                        data-application_no='{$item->application_no}'
                                                        data-applicant_full_name='{$item->applicant_full_name}'
                                                        data-applicant_taxid='{$item->applicant_taxid}'
                                                        data-created_at='{$created_at}'
                                                    ><i class='fa fa-trash-o' aria-hidden='true'></i>
                                                </button>";
                                }else{
                                    $button .= " <button type='button' class='btn btn-danger btn-xs btn_delete' disabled><i class='fa fa-trash-o' aria-hidden='true'></i></button>";
                                }
                                return $button;
                            })
                            ->order(function ($query) {
                                $query->orderBy('id', 'DESC');
                            })
                            ->rawColumns(['checkbox', 'action', 'branch_id', 'application_status', 'applicant_full_name'])
                            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $configs_evidences = ConfigsEvidence::leftjoin((new ConfigsEvidenceGroup)->getTable().' AS evidence_groups', 'evidence_groups.id', '=', 'configs_evidences.evidence_group_id')
                                            ->select('configs_evidences.*')
                                            ->where('configs_evidences.evidence_group_id', 2)
                                            ->where('configs_evidences.state', 1)
                                            ->where('evidence_groups.state', 1)
                                            ->orderBy('configs_evidences.ordering')
                                            ->get();
                                            // dd($configs_evidences);
        return view('section5/application_inspectors.create', compact('configs_evidences'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $requestData = $request->all();

            $user_login  = auth()->user();
            $user = is_null($user_login->ActInstead) ? $user_login : User::find($user_login->ActInstead->getKey());//ข้อมูลผู้ยื่น จาก ActInstead คือตัวแทนยื่นให้

            $gen_number =  HP::ConfigFormat( 'APP-Inspectors' , (new Section5ApplicationInspector)->getTable()  , 'application_no', null, null, null );
            $application_check = Section5ApplicationInspector::where('application_no', $gen_number)->first();
            if(!is_null($application_check)){
                $gen_number =  HP::ConfigFormat( 'APP-Inspectors' , (new Section5ApplicationInspector)->getTable()  , 'application_no', null, null, null );
            }
            $requestData['application_no']     = $gen_number;
            $requestData['created_by']         = $user->getKey();
            if( !empty( $requestData['type_save'] ) && $requestData['type_save'] == "save" ){ //ยื่นคำขอ อยู่ระหว่างการตรวจสอบ
                $requestData['application_status'] = 1;
                $requestData['application_date']   = date('Y-m-d');
            }else{ //ฉบับร่าง
                $requestData['application_status'] = 12;
            }

            $requestData['applicant_date_of_birth'] = !empty($requestData['applicant_date_of_birth'])?HP::convertDate($requestData['applicant_date_of_birth']):null;

            $requestData['configs_evidence']  = (count(HP::ConfigEvidence(2)) > 0)?json_encode(HP::ConfigEvidence(2), JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE):null;

            //เพิ่มข้อมูลที่อยู่ของผู้ยื่น
            $address = HP::GetIDAddress($user->subdistrict, $user->district, $user->province);
            $requestData['applicant_address']     = $user->address_no;
            $requestData['applicant_moo']         = $user->moo;
            $requestData['applicant_soi']         = $user->soi;
            $requestData['applicant_road']        = $user->street;
            $requestData['applicant_subdistrict'] = $address->subdistrict_id;
            $requestData['applicant_district']    = $address->district_id;
            $requestData['applicant_province']    = $address->province_id;
            $requestData['applicant_zipcode']     = $user->zipcode;

            $application = Section5ApplicationInspector::create($requestData);

            $this->SaveInspectorsSub($application, $requestData);
            $this->SaveFile($application, $request);

            return redirect('request_section5/application_inspectors')->with('flash_message', 'บันทึกข้อมูลเรียบร้อยแล้ว!');

        } catch (\Exception $e) {

            echo $e->getMessage();
            exit;
            return redirect('request_section5/application_inspectors')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Section5\Section5ApplicationInspector  $applicationInspector
     * @return \Illuminate\Http\Response
     */
    public function show(Section5ApplicationInspector $applicationInspector)
    {
        // $branch_scopes_query = Section5ApplicationInspectorsScope::where('application_id', $applicationInspector->id);
        $branch_scopes = Section5ApplicationInspectorsScope::where('application_id', $applicationInspector->id)
                                                            // ->leftjoin((new BranchGroup)->getTable().' AS branch_group', 'branch_group.id', '=', 'section5_application_inspectors_scope.branch_group_id')
                                                            ->leftjoin((new Branch)->getTable().' AS branch', 'branch.id', '=', 'section5_application_inspectors_scope.branch_id')
                                                            ->selectRaw('section5_application_inspectors_scope.*, branch.title as branch_title')
                                                            ->get()->keyBy('id')
                                                            ->groupBy('branch_group_id')
                                                            ->toArray();
        $branch_groups = BranchGroup::whereIn('id', Section5ApplicationInspectorsScope::where('application_id', $applicationInspector->id)->select('branch_group_id'))->pluck('title', 'id')->toArray();
        // $branchs = Branch::whereIn('id', $branch_scopes_query->select('branch_id'))->pluck('title', 'id')->toArray();
        // dd($branch_groups);
        $configs_evidences = ConfigsEvidence::leftjoin((new ConfigsEvidenceGroup)->getTable().' AS evidence_groups', 'evidence_groups.id', '=', 'configs_evidences.evidence_group_id')
                                            ->where('configs_evidences.evidence_group_id', 2)
                                            ->where('configs_evidences.state', 1)
                                            ->where('evidence_groups.state', 1)
                                            ->orderBy('configs_evidences.ordering')
                                            ->get();
        $app_configs_evidences = !empty($applicationInspector->configs_evidence)?json_decode($applicationInspector->configs_evidence):[];

        $application_inspectors_accepts = ApplicationInspectorsAccept::where('application_id', $applicationInspector->id)->get();

        $applicationInspector->show = true;

        return view('section5/application_inspectors.show', compact('applicationInspector', 'configs_evidences', 'app_configs_evidences', 'branch_scopes', 'branch_groups', 'application_inspectors_accepts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Section5\Section5ApplicationInspectors  $section5ApplicationInspectors
     * @return \Illuminate\Http\Response
     */
    public function edit(Section5ApplicationInspector $applicationInspector)
    {
        // $branch_scopes_query = Section5ApplicationInspectorsScope::where('application_id', $applicationInspector->id);
        $branch_scopes = Section5ApplicationInspectorsScope::where('application_id', $applicationInspector->id)
                                                            // ->leftjoin((new BranchGroup)->getTable().' AS branch_group', 'branch_group.id', '=', 'section5_application_inspectors_scope.branch_group_id')
                                                            ->leftjoin((new Branch)->getTable().' AS branch', 'branch.id', '=', 'section5_application_inspectors_scope.branch_id')
                                                            ->selectRaw('section5_application_inspectors_scope.*, branch.title as branch_title')
                                                            ->get()->keyBy('id')
                                                            ->groupBy('branch_group_id')
                                                            ->toArray();
        $branch_groups = BranchGroup::whereIn('id', Section5ApplicationInspectorsScope::where('application_id', $applicationInspector->id)->select('branch_group_id'))->pluck('title', 'id')->toArray();
        // $branchs = Branch::whereIn('id', $branch_scopes_query->select('branch_id'))->pluck('title', 'id')->toArray();
        // dd($branch_groups);
        $configs_evidences = ConfigsEvidence::leftjoin((new ConfigsEvidenceGroup)->getTable().' AS evidence_groups', 'evidence_groups.id', '=', 'configs_evidences.evidence_group_id')
                                            ->where('configs_evidences.evidence_group_id', 2)
                                            ->where('configs_evidences.state', 1)
                                            ->where('evidence_groups.state', 1)
                                            ->orderBy('configs_evidences.ordering')
                                            ->get();
        $app_configs_evidences = !empty($applicationInspector->configs_evidence)?json_decode($applicationInspector->configs_evidence):[];

        $application_inspectors_accepts = ApplicationInspectorsAccept::where('application_id', $applicationInspector->id)->get();

        $applicationInspector->edited = true;

        return view('section5/application_inspectors.edit', compact('applicationInspector', 'configs_evidences', 'app_configs_evidences', 'branch_scopes', 'branch_groups', 'application_inspectors_accepts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Section5\Section5ApplicationInspectors  $applicationInspector
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section5ApplicationInspector $applicationInspector)
    {
        try {

            $requestData = $request->all();

            if(empty($applicationInspector->application_no)){
                $gen_number =  HP::ConfigFormat( 'APP-Inspectors' , (new Section5ApplicationInspector)->getTable()  , 'application_no', null, null, null );
                $requestData['application_no'] = $gen_number;
            }

            $requestData['updated_by'] = auth()->user()->getKey();
            // $requestData['status_application'] = 1;
            // $requestData['date_application'] = date('Y-m-d');

            $requestData['applicant_date_of_birth'] = !empty($requestData['applicant_date_of_birth'])?HP::convertDate($requestData['applicant_date_of_birth']):null;

            if(empty($applicationInspector->configs_evidence)){
                $requestData['configs_evidence']  = (count(HP::ConfigEvidence(2)) > 0)?json_encode(HP::ConfigEvidence(2), JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE):null;
            }

            if( !empty( $requestData['type_save'] ) && $requestData['type_save'] == "save" ){ //ยื่นคำขอ อยู่ระหว่างการตรวจสอบ
                $requestData['application_status'] = 1;
                if(is_null($applicationInspector->application_date)){
                    $requestData['application_date']   = date('Y-m-d');
                }
            }else{ //ฉบับร่าง
                $requestData['application_status'] = 12;
            }

            if(@$applicationInspector->application_status == 3){
                $requestData['application_status'] = 1;
            }

            $applicationInspector->update( $requestData );

            $this->SaveInspectorsSub( $applicationInspector, $requestData );
            $this->SaveFile( $applicationInspector, $request );

            return redirect('request_section5/application_inspectors')->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว!');

        } catch (\Exception $e) {

            echo $e->getMessage();
            exit;
            return redirect('request_section5/application_inspectors')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Section5\Section5ApplicationInspector  $applicationInspector
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section5ApplicationInspector $applicationInspector)
    {
        //
    }

    public function SaveInspectorsSub($application, $requestData)
    {
        $branchs = !empty($requestData['repeater-branch']) ? $requestData['repeater-branch'] : [];

        if(!empty($application->id)){
            $branch_all = [];
            foreach($branchs as $key=>$branch){
              $branch_all =  array_merge($branch_all, $branch['old_id']);
            }
            $old_ids = array_diff($branch_all, [null]);

            Section5ApplicationInspectorsScope::where('application_id', $application->id)->when($old_ids, function($query, $old_ids){
                                    $query->whereNotIn('id', $old_ids);
                                })->delete();
            if(!empty($branchs) && count($branchs) > 0){
                foreach($branchs as $key => $branch){

                    foreach($branch['branch_id'] as $k => $val){

                        //บันทึกขอบข่าย
                        $scope = Section5ApplicationInspectorsScope::updateOrCreate(
                                    ['id' => $branch['old_id'][$k]],
                                    [
                                        'application_id' => $application->id,
                                        'application_no' => $application->application_no,
                                        'branch_id' => $val,
                                        'branch_group_id' => $branchs[$key]['branch_group_id'],
                                        'created_by' => auth()->user()->getKey()
                                    ]
                                );

                        //บันทึกมอก.ของขอบข่าย
                        $tis_ids = array_key_exists('tis_id', $branchs[$key]) ? $branchs[$key]['tis_id'] : [] ;
                        foreach ($tis_ids as $tis_id) {

                            $standard   = Tis::find($tis_id);
                            $branch_tis = BranchTis::where('tis_id', $tis_id)->pluck('branch_id')->toArray();//ข้อมูลมาตรฐานที่ผูกอยู่กับรายสาขา

                            if(!is_null($standard) && (count($branch_tis) > 0 && in_array($val, $branch_tis))){//เป็นมาตรฐานของรายสาขานี้

                                Section5ApplicationInspectorsScopeTis::updateOrCreate(
                                    ['inspector_scope_id' => $scope->id,
                                     'tis_id' => $standard->getKey()
                                    ],
                                    [
                                        'inspector_scope_id' => $scope->id,
                                        'application_no'     => $application->application_no,
                                        'tis_id'             => $standard->getKey(),
                                        'tis_no'             => $standard->tb3_Tisno,
                                        'tis_name'           => $standard->tb3_TisThainame
                                    ]
                                );

                            }

                        }

                    }

                }
            }
        }
    }

    public function SaveFile( $application ,  $request)
    {

        $requestData = $request->all();

        $attach_path =  $this->attach_path.'/Section5/ApplicationInspectors/'.$application->application_no;

        if( !empty( $requestData['evidences'] ) && is_array($requestData['evidences']) && count($requestData['evidences']) > 0){

            $evidences = $requestData['evidences'];

            foreach( $evidences as $evidence ){

                if( !empty($evidence['evidence_file_config']) ){
                    HP::singleFileUpload(
                        $evidence['evidence_file_config'],
                        $attach_path,
                        (auth()->user()->tax_number ?? null),
                        (auth()->user()->username ?? null),
                        'SSO',
                        (  (new Section5ApplicationInspector)->getTable() ),
                        $application->id,
                        'evidence_file_config',
                        !empty($evidence['setting_title'])?$evidence['setting_title']:null,
                        !empty($evidence['setting_id'])?$evidence['setting_id']:null
                    );
                }

            }

        }

        if( !empty( $requestData['repeater-file'] ) ){

            $repeater_file = $requestData['repeater-file'];

            foreach( $repeater_file as $file ){

                if( isset($file['evidence_file_other']) && !empty($file['evidence_file_other']) ){
                    HP::singleFileUpload(
                        $file['evidence_file_other'],
                        $attach_path,
                        (auth()->user()->tax_number ?? null),
                        (auth()->user()->username ?? null),
                        'SSO',
                        (  (new Section5ApplicationInspector)->getTable() ),
                        $application->id,
                        'evidence_file_other',
                        !empty($file['file_documents'])?$file['file_documents']:null
                    );
                }

            }

        }

    }

    public function AutoRunRefApplication()
    {
        $today = date('Y-m-d');
        $dates = explode('-', $today);
        $year = ( date('y')  + 43);
        $ref = 'LAB';

        $query_check = Section5ApplicationInspector::select('application_no')->whereYear('created_at', $dates[0])->whereMonth('created_at', $dates[1])->orderBy('application_no')->get();
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

            $no_check = Section5ApplicationInspector::where('application_no', $strNextSeq )->first();
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

    public function search_users(Request $request)
    {

        $search_query = $request->get('query');
        $search = str_replace(' ', '', $search_query);

        $subdistricts = Subdistrict::selectRaw('DISTRICT_ID, TRIM(DISTRICT_NAME) AS DISTRICT_NAME')->pluck('DISTRICT_NAME', 'DISTRICT_ID')->toArray();
        $districts = District::selectRaw('AMPHUR_ID, TRIM( REPLACE(AMPHUR_NAME,"เขต","") ) AS AMPHUR_NAME')->pluck('AMPHUR_NAME', 'AMPHUR_ID')->toArray();
        $provinces = Province::selectRaw('PROVINCE_ID, TRIM(PROVINCE_NAME) AS PROVINCE_NAME')->pluck('PROVINCE_NAME', 'PROVINCE_ID')->toArray();

        $data = User::where(function($query) use($search) {
                        $query->where('tax_number', 'LIKE' ,'%'.$search.'%')
                                ->OrWhere(DB::raw("REPLACE(`name`, ' ', '')"), 'LIKE', "%".$search."%");
                    })
                    ->where(function($query) {
                        $query->WhereNotNull('tax_number');
                    })
                    ->where(function($query) {
                        $query->WhereNotIn('id', [ auth()->user()->id ] );
                    })
                    ->select(
                        'id', 'tax_number', 'contact_prefix_text', 'contact_first_name', 'contact_last_name', 'name',
                        'address_no', 'building','street', 'moo','soi','subdistrict','district','province','zipcode',
                        'contact_tel', 'contact_phone_number', 'branch_type', 'branch_code'
                    )
                    ->get();

        foreach( $data as $item ){
            $address = HP::GetIDAddress( $item->subdistrict, $item->district, $item->province );
            $item->name_full =  $item->name;
            $item->name =  $item->TypeaheadDropdownTitle;
            $item->taxid = $item->tax_number;
            $item->agency_subdistrict_id = @$address->subdistrict_id;
            $item->agency_district_id = @$address->district_id;
            $item->agency_province_id = @$address->province_id;
            $item->agency_subdistrict_title = array_key_exists(@$address->subdistrict_id, $subdistricts)?$subdistricts[@$address->subdistrict_id]:null;
            $item->agency_district_title = array_key_exists(@$address->district_id, $districts)?$districts[@$address->district_id]:null;
            $item->agency_province_title = array_key_exists(@$address->province_id, $provinces)?$provinces[@$address->province_id]:null;
        }

        return response()->json($data,JSON_UNESCAPED_UNICODE);

    }

    public function getOptionBranch($branch_group_id)
    {
        $branchs = Branch::where('branch_group_id',$branch_group_id)->where('state', 1)->select('id','title')->get();
        return response()->json($branchs,JSON_UNESCAPED_UNICODE);
    }

    public function delete_update(Request $request)
    {
        try {

            $id = $request->get('id');
            $application_inspectors = Section5ApplicationInspector::findOrFail($id);

            $requestData = $request->all();
            $requestData['application_status'] = 11;
            $requestData['delete_by'] = auth()->user()->getKey();
            $requestData['delete_at'] = date('Y-m-d h:i:s');

            $application_inspectors->update($requestData);

            if( $application_inspectors ){
                return 'success';
            }else{
                return 'error';
            }

        } catch (\Exception $e) {

            return 'error';

        }
    }

}
