<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section5\ApplicationIbcbInspectorsScope;
class ApplicationIbcbInspectors extends Model
{
    protected $table = 'section5_application_ibcb_inspectors';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'application_id',
        'application_no',
        'inspector_id',
        'inspector_prefix',
        'inspector_first_name',
        'inspector_last_name',
        'inspector_taxid',
        'inspector_type'
        
    ];

    public function getInspectorFullNameAttribute() {
        return (!empty($this->inspector_prefix)?$this->inspector_prefix:null).($this->inspector_first_name).' '.($this->inspector_last_name);
    }

    public function scopes(){
        return $this->hasMany(ApplicationIbcbInspectorsScope::class, 'ibcb_inspector_id');
    } 

    public function getScopeShowAttribute(){

        $app_scope = $this->scopes()->select('branch_group_id')->groupBy('branch_group_id')->get();

        $html = '<ul  class="list-unstyled">';
        foreach( $app_scope AS $item ){
            $bs_branch_group = $item->bs_branch_group;

            if( !is_null($bs_branch_group) ){
     
                $html .= '<li>'.($bs_branch_group->title).'</li>';
                $scope = $this->scopes()->where('branch_group_id', $bs_branch_group->id )->select('branch_id')->get();
                $html .= '<li>';
                $html .= '<ul>';
                $list = [];
                foreach( $scope as $branch ){
                    $bs_branch =  $branch->bs_branch;
                    $list[] = $bs_branch->title;
                }
                $html .= '<li>'.( implode( ' ,',  $list ) ).'</li>';
                $html .= '</ul>';
                $html .= '</li>';
            }

        }
        $html .= '</ul>';
        
        return $html;
    }

    public function getScopeBranchInputAttribute(){
        $group = $this->scopes()->select('branch_group_id')->groupBy('branch_group_id')->get();

        $html = '';
        foreach( $group AS $item ){

            $branch_arr = $this->scopes()->where('branch_group_id', $item->branch_group_id )->select('branch_id')->pluck('branch_id')->toArray();
            $html = '<input type="hidden" name="branch_id_'.($item->branch_group_id).'" value="'.((count($branch_arr) > 0 )?implode(',', $branch_arr):null ).'">';
        }

        return $html;
    }

}
