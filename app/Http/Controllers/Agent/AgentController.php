<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\Agents\Agent;
use App\Models\Agents\AgentSystem;
use App\Models\Config\SettingSystem;

use App\User;
use HP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

use App\Sessions;
use Session;

use App\Http\Controllers\Auth\LoginController;

class AgentController extends Controller
{

    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        $this->middleware('auth');
        $this->attach_path = 'files/sso';

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        HP::UserAgentExpire();

        $keyword = $request->get('search');
        $perPage = 25;

        $filter['filter_state'] = $request->get('filter_state', '');
        $filter['filter_search'] = $request->get('filter_search', '');
        $filter['filter_start_date'] = $request->get('filter_start_date', '');
        $filter['filter_start_date'] = $request->get('filter_start_date', '');
        $filter['filter_end_date'] = $request->get('filter_end_date', '');
        $filter['perPage'] = $request->get('perPage', 25);

        $Query = new Agent();

        if($filter['filter_search'] != '') {
            $Query = $Query->where(function($query) use($filter) {
                                $query->where('agent_name', 'LIKE', '%' . $filter['filter_search'] . '%')
                                    ->orwhere('agent_taxid', 'LIKE', '%' . $filter['filter_search'] . '%')
                                    ->orwhere('head_name', 'LIKE', '%' . $filter['filter_search'] . '%')
                                    ->orwhere('user_taxid', 'LIKE', '%' . $filter['filter_search'] . '%');
                            });
        }

        if( $filter['filter_state'] != '' ){
            $Query =  $Query->where( 'state', $filter['filter_state'] );
        }else{
            $Query =  $Query->whereNotIn( 'state', [ 99 ] );
        }

        $Query =  $Query->where(function($query) {
                        $query->Where('user_id',  auth()->user()->id  );
                    });

        $agent = $Query->sortable()->paginate($filter['perPage']);

        return view('agents/agent.index', compact('agent', 'filter'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $table_user  = (new User)->getTable();
        $table_agent = (new Agent)->getTable();
        $user_id = auth()->user()->getKey();
        $agents  = Agent::where($table_agent.'.user_id', $user_id)
                        ->where(function($query){
                            $query->where('issue_type', 1)
                                  ->orWhere(function($query){
                                    $now = date('Y-m-d');
                                    $query->where('issue_type', 2)
                                          ->whereDate('end_date', '>=', $now);
                                  });
                        })
                        ->whereIn($table_agent.'.state', [1, 2])//สถานะ 1=มอบสิทธิ์(รอดำเนินการยืนยัน), 2=ดำเนินการตามรับมอบ
                        ->with('user_agent_created')
                        ->with('agent_detail')
                        ->get();

        $setting_systems = SettingSystem::pluck('title', 'id')->toArray();

        $agent_list = [];
        foreach ($agents as $key => $agent) {
            if(!is_null($agent->user_agent_created)){//ยังมี user นี้อยู่

                $agent_user = $agent->user_agent_created;

                $tmp = (object)[];
                $tmp->id           = $agent->id;
                $tmp->user_id      = $agent_user->id;
                $tmp->name         = $agent_user->name;
                $tmp->tax_number   = $agent_user->tax_number;
                $tmp->created_at   = HP::DateTimeThai($agent->created_at);
                $tmp->confirm_date = !empty($item->confirm_date) ? HP::DateTimeThai($agent->confirm_date) : '-';
                $tmp->system_text  = $agent->AgentSystem;
                $tmp->condition_text = $agent->issue_type == 1 ? 'ตลอดเวลา' : ( !empty($agent->start_date) && !empty($agent->end_date) ? 'วันที่ ' .HP::DateThai($agent->start_date).' - '.HP::DateThai($agent->end_date) : '' );
                $tmp->state_text   = $agent->StateText;
                $tmp->select_all   = $agent->select_all;//1=มอบทุกระบบ
                $tmp->issue_type   = $agent->issue_type;//ประเภทช่วงเวลาที่มอบสิทธิ์
                $tmp->start_date   = $agent->start_date;//วันที่เริ่ม
                $tmp->end_date     = $agent->end_date;//วันที่สิ้นสุด

                if($agent->select_all==1){//มอบสิทธิ์ให้ทุกระบบ
                    $tmp->system_ids = null;
                }else{//ระบบที่มอบสิทธิ์ให้
                    if(count($agent->agent_detail) > 0){
                        $system_ids = $agent->agent_detail->keyBy('setting_systems_id')->toArray(); //id ระบบที่มอบสิทธิ์ให้
                        $tmp->system_ids = array_intersect_key($setting_systems, $system_ids);//เอา id ระบบที่มอบสิทธิ์ให้ join กับชื่อระบบทั้งหมด
                    }
                }

                $agent_list[] = $tmp;
            }
        }

        return view('agents/agent.create', compact('agent_list'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        try {

            $requestData = $request->all();
            $requestData['state'] = 1;
            $requestData['created_by'] = auth()->user()->getKey();

            $requestData['start_date'] = !empty($requestData['start_date'])?HP::convertDate($requestData['start_date']):null;
            $requestData['end_date'] = !empty($requestData['end_date'])?HP::convertDate($requestData['end_date']):null;

            $requestData['select_all'] = !empty($requestData['select_all'])?$requestData['select_all']:null;

            $agent =  Agent::create($requestData);

            if( isset( $requestData['repeater-file'] ) ){

                $repeater_file = $requestData['repeater-file'];

                foreach( $repeater_file as $file ){

                    if( isset($file['file_attach']) && !empty($file['file_attach']) ){
                        HP::singleFileUpload(
                            $file['file_attach'],
                            $this->attach_path,
                            (auth()->user()->tax_number ?? null),
                            (auth()->user()->username ?? null),
                            'SSO',
                            (self::getTableName()),
                            $agent->id,
                            'file_attach',
                            !empty($file['file_documents'])?$file['file_documents']:null
                        );
                    }

                }

            }

            if( isset( $requestData['setting_system'] ) ){

                $setting_system = $requestData['setting_system'];

                foreach( $setting_system  as $item ){

                    $data = AgentSystem::where('sso_agent_id', $agent->id )->where('setting_systems_id', $item )->first();
                    if( is_null( $data ) ){
                        $data = new AgentSystem;
                    }
                    $data->sso_agent_id = $agent->id;
                    $data->setting_systems_id = $item;
                    $data->save();
                }

            }
            return redirect('agents')->with('flash_message', 'Agent added!');

            if($request->previousUrl){
                return redirect("$request->previousUrl")->with('flash_message', 'เรียบร้อยแล้ว!');
            }else{
                return redirect('agents')->with('flash_message', 'เรียบร้อยแล้ว!');
            }

        } catch (\Exception $e) {

            echo $e->getMessage();
            exit;
            return redirect('agents')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        }

    }

    public static function getTableName()
    {
        $model = new Agent();
        return $model->getTable();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $agent = Agent::findOrFail($id);
        return view('agents/agent.show', compact('agent'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $agent = Agent::findOrFail($id);
        return view('agents/agent.edit', compact('agent'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $agent = Agent::findOrFail($id);

        try {

            $requestData = $request->all();

            $requestData['start_date'] = !empty($requestData['start_date'])?HP::convertDate($requestData['start_date']):null;
            $requestData['end_date'] = !empty($requestData['end_date'])?HP::convertDate($requestData['end_date']):null;

            $requestData['select_all'] = !empty($requestData['select_all'])?$requestData['select_all']:null;

            if( isset( $requestData['setting_system'] ) ){

                $setting_system = $requestData['setting_system'];

                foreach( $setting_system  as $item ){

                    $data = AgentSystem::where('sso_agent_id', $agent->id )->where('setting_systems_id', $item )->first();
                    if( is_null( $data ) ){
                        $data = new AgentSystem;
                    }
                    $data->sso_agent_id = $agent->id;
                    $data->setting_systems_id = $item;
                    $data->save();
                }

            }else{
                AgentSystem::where('sso_agent_id', $agent->id )->delete();
            }


            if( isset( $requestData['repeater-file'] ) ){

                $repeater_file = $requestData['repeater-file'];

                foreach( $repeater_file as $file ){

                    if( isset($file['file_attach']) && !empty($file['file_attach']) ){
                        HP::singleFileUpload(
                            $file['file_attach'],
                            $this->attach_path,
                            (auth()->user()->tax_number ?? null),
                            (auth()->user()->username ?? null),
                            'SSO',
                            (self::getTableName()),
                            $agent->id,
                            'file_attach',
                            !empty($file['file_documents'])?$file['file_documents']:null
                        );
                    }

                }

            }


            $agent->update($requestData);

            return redirect('agents')->with('flash_message', 'Agent updated!');

        } catch (\Exception $e) {
            return redirect('agents')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $agent = Agent::where('id', $id )->update(['state' => 99]);

        if( $agent ){
            return redirect('agents')->with('flash_message', 'Agent deleted!');
        }


    }

    public function search_users(Request $request)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $search_query = $request->get('query');
        $search = str_replace(' ', '', $search_query);

        $data = User::where(function($query) use($search) {
                        $query->where('tax_number', 'LIKE' ,'%'.$search.'%')
                                ->OrWhere(DB::raw("REPLACE(`name`, ' ', '')"), 'LIKE', "%".$search."%");
                    })
                    // ->where(function($query) {
                    //     $query->WhereNotNull('tax_number')->WhereNotNull('contact_last_name');
                    // })
                    ->where(function($query) {
                        $query->Where('block',0);
                    })
                    ->where(function($query) {
                        $query->WhereNotIn('id', [ auth()->user()->id ] );
                    })
                    ->select(
                        // DB::raw("CONCAT(contact_prefix_text,contact_first_name,' ',contact_last_name,' | ',tax_number) AS name") ,
                        // DB::raw("CONCAT(contact_prefix_text,contact_first_name,' ',contact_last_name) AS names"),
                        'id', 'tax_number', 'contact_prefix_text', 'contact_first_name', 'contact_last_name', 'name',
                        'address_no', 'building','street', 'moo','soi','subdistrict','district','province','zipcode', 'contact_tel', 'contact_phone_number',
                        'branch_type', 'branch_code'

                    )
                    ->get();

        foreach( $data as $item ){
            $branch          = $item->branch_type==2 ? 'สาขา '. $item->branch_code : '' ;
            $item->name_full = $item->name;
            $item->name      = $item->name . ' ' . $branch . ' | ' . $item->tax_number;
        }

        return response()->json($data);

    }

    public function up_act_instead(Request $request)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $user_id = auth()->user()->getKey();

        $status = false;
        $message = '';

        if($request->act_instead_id == 0){//ในนามตัวเอง
            $act_instead = null;
        }else{//ในฐานะผู้รับมอบอำนาจ
            $act_instead = $request->act_instead_id ?? null;

            //เช็คข้อมูล
            $user = User::find($act_instead);
            if(in_array($user->applicanttype_id, [1, 2, 3])) {//1=นิติบุคคล, 2=บุคคลธรรมดา, 3=คณะบุคคล
                $login = new LoginController;
                if($user->applicanttype_id == 1){//นิติบุคคล
                    $entity = HP::getJuristic($user->tax_number, $request->ip());
                    if(property_exists($entity, 'status')){
                        if($entity->status==='fail'){//ไม่พบข้อมูล
                            $message = 'ไม่พบข้อมูลเลขนิติบุคคล '.$user->tax_number.' ขึ้นทะเบียนกับกรมพัฒนาธุรกิจการค้า';
                            goto end;
                        }elseif($entity->status==='success'){//พบข้อมูล
                            $juristic_status = ['ยังดำเนินกิจการอยู่' => '1', 'ฟื้นฟู' => '2', 'คืนสู่ทะเบียน' => '3'];
                            if(array_key_exists(trim($entity->JuristicStatus), $juristic_status)){//สถานะดำเนินกิจการ
                                $message = $login->compareCompanyAndUpdate($user, $entity);//เปรียบเทียบข้อมูล และอัพเดทลงฐานข้อมูล
                            }else{
                                $message = 'เลขนิติบุคคล '.$user->tax_number.' ไม่อยู่ในสถานะดำเนินกิจการ';
                                goto end;
                            }
                        }else{//อื่นๆ... ให้ใช้งานได้

                        }
                    }
                }elseif($user->applicanttype_id == 2){//บุคคลธรรมดา
                    $personal = HP::getPersonal($user->tax_number, $request->ip());

                    if(is_null($personal) || (is_object($personal) && property_exists($personal, 'Code') && $personal->Code == '00404')){//ไม่พบข้อมูลในทะเบียนราษฎร์
                        $message = 'ขออภัยเลขประจำตัวประชาชน '. $user->tax_number . ' ไม่พบในทะเบียนราษฎร์กรุณาติดต่อเจ้าหน้าที่';
                        goto end;
                    }elseif($personal->status === 'no-connect'){ // request api ไม่ได้ อนุญาตให้ใช้งานได้

                    }elseif(property_exists($personal, 'statusOfPersonCode') && $personal->statusOfPersonCode == '1'){//เสียชีวิต
                        $message = 'เลขประจำตัวประชาชน '. $user->tax_number . ' ไม่สามารถใช้งานได้ เนื่องจากมีสถานะเป็น:&nbsp;<u>เสียชีวิต</u>';
                        goto end;
                    }else{//ให้ใช้งานได้
                        $message = $login->comparePersonAndUpdate($user, $personal);//เปรียบเทียบข้อมูล และอัพเดทลงฐานข้อมูล
                    }
                }elseif ($user->applicanttype_id == 3) {//คณะบุคคล
                    $rd = HP::getRdVat($user->tax_number, $request->ip());
                    if(!empty($rd->vMessageErr)){//ไม่พบข้อมูลในสรรพากร
                        $message = 'ขออภัยเลขประจำตัวผู้เสียภาษี '. $user->tax_number . ' ไม่พบในกรมสรรพากรกรุณาติดต่อเจ้าหน้าที่';
                        goto end;
                    }elseif($rd->status === 'no-connect'){ // request api ไม่ได้ ให้ login ได้

                    }else{// Login ปกติ
                        $message = $login->compareRdAndUpdate($user, $rd);//เปรียบเทียบข้อมูล และอัพเดทลงฐานข้อมูล
                    }
                }

            }

        }

        //ลบ Session เดิม
        Sessions::Remove(session()->getId());

        //Gen Session ใหม่
        session()->regenerate(true);
        $session_id = session()->getId();

        //บันทึกลงตาราง session
        Sessions::Add(
                    $session_id,
                    $user_id,
                    $request->ip(),
                    $request->userAgent(),
                    'web',
                    $act_instead
                );

        //บันทึกลง response cookie
        $config = HP::getConfig();
        $minutes = config('session.lifetime');
        Cookie::queue($config->sso_name_cookie_login,
                      $session_id,
                      $minutes,
                      null,
                      $config->sso_domain_cookie_login,
                      null,
                      false
                  );

        if(!is_null($session_id)){
            $status = true;
        }

        end:
        return response()->json(['status' => $status, 'urls' => $request->urls ?? url(''), 'message' => $message, '_token' => csrf_token()]);

    }

    //อัพเดทข้อมูลผู้มอบอำนาจจาก API
    public function update_instead_api(Request $request){

        $status = 'fail';

        $login = new LoginController;
        $act_instead = $request->get('act_instead_id');
        $user = User::find($act_instead);
        if(in_array($user->applicanttype_id, [1, 2, 3])){
            if($user->applicanttype_id==1){//นิติบุคคล
                $entity = HP::getJuristic($user->tax_number, $request->ip());
                if(property_exists($entity, 'status')){
                    if($entity->status==='success'){//พบข้อมูล
                        $login->compareCompanyAndUpdate($user, $entity);//เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล
                        $status  = 'success';
                    }
                }
            }elseif($user->applicanttype_id==2){//บุคคลธรรมดา
                $personal = HP::getPersonal($user->tax_number, $request->ip());
                if(property_exists($personal, 'status')){
                    if($personal->status==='success'){//พบข้อมูล
                        $login->comparePersonAndUpdate($user, $personal);//เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล
                        $status  = 'success';
                    }
                }
            }elseif($user->applicanttype_id==3){//ตณะบุคคล
                $rd = HP::getRdVat($user->tax_number, $request->ip());
                if(property_exists($rd, 'status')){
                    if($rd->status==='success'){//พบข้อมูล
                        $login->compareRdAndUpdate($user, $rd);//เปรียบเทียบข้อมูลและอัพเดทลงฐานข้อมูล
                        $status  = 'success';
                    }
                }
            }
        }

        return response()->json(['status' => $status]);

    }

    public function delete_update(Request $request)
    {

        $this->check_permission_branch();//เช็คสิทธิ์สาขา

        $id = $request->get('id');
        $requestData = $request->all();

        unset($requestData['_token']);

        $requestData['remarks_delete'] = !empty($requestData['remarks_delete'])?$requestData['remarks_delete']:null;

        $agent = Agent::findOrFail($id);

        if( $agent->issue_type == 2 && $agent->end_date >= date('Y-m-d') && $agent->state == 2 ){ // เคสที่มายกเลิกก่อนหมดอายุ

            $requestData['state'] = 3;

        }else if( $agent->issue_type == 2 && $agent->end_date < date('Y-m-d') && $agent->state == 2 ){ // เคสที่ดำเนินการตามรับมอบแล้วมายกเลิก

            $requestData['state'] = 4;

        }else if( $agent->issue_type == 1 && $agent->state == 2 ){ // เคสที่ดำเนินการตามรับมอบแล้วมายกเลิก

            $requestData['state'] = 3;

        }else{  // เคสที่มอบสิทธิ์แล้วมายกเลิก

            $requestData['state'] = 99;

        }

        $requestData['delete_by'] = auth()->user()->getKey();;
        $requestData['delete_at'] = date('Y-m-d h:i:s');

        $agent->update($requestData);

        if( $agent ){
            echo 'success';
        }else{
            echo 'error';
        }

    }

    private function check_permission_branch(){
        if(auth()->user()->branch_type==2){
            abort(403);
        }
    }

}
