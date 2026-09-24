<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use HP;
use Illuminate\Http\Request;
use App\Models\Agents\Agent;
class ConfirmAgentController extends Controller
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

        //$this->check_permission_branch();//เช็คสิทธิ์สาขา

            HP::UserAgentExpire();
            $filter = [];
            $keyword = $request->get('search');

            $filter['filter_state'] = $request->get('filter_state', '');
            $filter['filter_search'] = $request->get('filter_search', '');
            $filter['filter_start_date'] = $request->get('filter_start_date', '');
            $filter['filter_start_date'] = $request->get('filter_start_date', '');
            $filter['filter_end_date'] = $request->get('filter_end_date', '');
            $filter['perPage'] = $request->get('perPage', 25);

            $Query = new Agent();

            if ($filter['filter_search'] != '') {
                $Query = $Query->where(function($query) use($filter) {
                    $query->where('agent_name', 'LIKE', '%' . $filter['filter_search'] . '%')
                        ->orwhere('agent_taxid', 'LIKE', '%' . $filter['filter_search'] . '%')
                        ->orwhere('head_name', 'LIKE', '%' . $filter['filter_search'] . '%')
                        ->orwhere('user_taxid', 'LIKE', '%' . $filter['filter_search'] . '%');
                });
            }

            if ($filter['filter_state'] != '') {
                $Query =   $Query->where( 'state', $filter['filter_state']);
            }

            $Query =  $Query->where('agent_id', auth()->user()->id)->where('state','!=','99');

            $agent = $Query->sortable()
                           ->with('user_head_created')
                           ->paginate($filter['perPage']);

            return view('agents/confirm_agent.index', compact('agent','filter'));

    }


    public function edit($id)
    {

        //$this->check_permission_branch();//เช็คสิทธิ์สาขา

            $agent                          = Agent::findOrFail($id);
            // $agent->tax_id                  = !empty($agent->user_agent_created->contact_tax_id) ? $agent->user_agent_created->contact_tax_id : null;
            // $agent->contact_tel             = !empty($agent->user_agent_created->contact_tel) ? $agent->user_agent_created->contact_tel : null;
            // $agent->contact_phone_number    = !empty($agent->user_agent_created->contact_phone_number) ? $agent->user_agent_created->contact_phone_number : null;

            // if(!empty($agent) ){
            //     $agent->head_address_no     =   implode(' ', self::head_address_no($agent)) ;
            //     $agent->agent_address_no    =   implode(' ', self::agent_address_no($agent)) ;
            // }

            return view('agents/confirm_agent.edit', compact('agent'));

    }


    public function update(Request $request, $id)
    {

        //$this->check_permission_branch();//เช็คสิทธิ์สาขา

        try {
                $requestData                   = $request->all();
                if($request->confirm_status == 1){
                    $requestData['state']   = 2;
                }else{
                    $requestData['state']   = 5;
                }
                $requestData['confirm_status'] = isset($request->confirm_status) ? $request->confirm_status : null;
                $requestData['confirm_date']   = date('Y-m-d H:i:s');
                $agent  = Agent::findOrFail($id);
                $agent->update($requestData);

                // if($request->hasFile('attach')){
                //          HP::singleFileUpload($request->attach,
                //                          $this->attach_path,
                //                         (auth()->user()->tax_number ?? null),
                //                         (auth()->user()->username ?? null),
                //                         'SSO',
                //                         (self::getTableName()),
                //                         $agent->id,
                //                         '2',
                //                         $request->file_attach ?? null
                //         );
                // }

             if($request->previousUrl){
                return redirect("$request->previousUrl")->with('flash_message', 'เรียบร้อยแล้ว!');
              }else{
                  return redirect('confirm-agents')->with('flash_message', 'เรียบร้อยแล้ว!');
              }
         } catch (\Exception $e) {
                return redirect('confirm-agents')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
         }

    }
    public static function getTableName()
    {
        $model = new Agent();
        return $model->getTable();
    }

	public static function head_address_no($request){
        $address   =  [];
        $address[] = $request->head_address_no;

        if($request->head_village!='' && $request->head_village !='-'  && $request->head_village !='--'){
            $address['head_village'] =  " อาคาร/หมู่บ้าน"  . $request->head_village;
        }
        if($request->head_moo!=''){
          $address['head_moo'] =  " หมู่ที่" . $request->head_moo;
        }
        if($request->head_soi!='' && $request->head_soi !='-'  && $request->head_soi !='--'){
          $address['head_soi'] =  " ซอย"  . $request->head_soi;
        }
        if($request->head_subdistrict!=''){
            if($request->head_province=='กรุงเทพมหานคร'){
                $address['head_subdistrict'] = " แขวง".$request->head_subdistrict;
            }else{
                $address['head_subdistrict'] = " ตำบล".$request->head_subdistrict;

            }
        }

        if($request->head_district!=''){
            if($request->head_province=='กรุงเทพมหานคร'){
                $address['head_district'] = " เขต".$request->head_district;
            }else{
                $address['head_district'] = " อำเภอ".$request->head_district;
            }
        }

        if($request->head_province!=''){
            if($request->head_province=='กรุงเทพมหานคร'){
                $address['head_province'] =  " ".$request->head_province;
            }else{
                $address['head_province'] =  " จังหวัด".$request->head_province;
            }
        }

        if($request->head_zipcode!=''){
            $address['head_zipcode'] =  $request->head_zipcode;
          }
        return  $address;
   }

   	public static function agent_address_no($request){
        $address   =  [];
        $address[] = $request->agent_address_no;

        if($request->agent_village!='' && $request->agent_village !='-'  && $request->agent_village !='--'){
            $address['agent_village'] =  " อาคาร/หมู่บ้าน"  . $request->agent_village;
        }
        if($request->agent_moo!=''){
          $address['agent_moo'] =  " หมู่ที่" . $request->agent_moo;
        }
        if($request->agent_soi!='' && $request->agent_soi !='-'  && $request->agent_soi !='--'){
          $address['agent_soi'] =  " ซอย"  . $request->agent_soi;
        }
        if($request->agent_subdistrict!=''){
            if($request->agent_province=='กรุงเทพมหานคร'){
                $address['agent_subdistrict'] = " แขวง".$request->agent_subdistrict;
            }else{
                $address['agent_subdistrict'] = " ตำบล".$request->agent_subdistrict;

            }
        }

        if($request->agent_district!=''){
            if($request->agent_province=='กรุงเทพมหานคร'){
                $address['agent_district'] = " เขต".$request->agent_district;
            }else{
                $address['agent_district'] = " อำเภอ".$request->agent_district;
            }
        }

        if($request->agent_province!=''){
            if($request->agent_province=='กรุงเทพมหานคร'){
                $address['agent_province'] =  " ".$request->agent_province;
            }else{
                $address['agent_province'] =  " จังหวัด".$request->agent_province;
            }
        }

        if($request->agent_zipcode!=''){
            $address['agent_zipcode'] =  $request->agent_zipcode;
         }
        return  $address;
    }

    private function check_permission_branch(){
        if(auth()->user()->branch_type==2){
           abort(403);
        }
    }

}
