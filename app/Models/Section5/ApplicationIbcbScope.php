<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Basic\BranchGroup;
use App\Models\Section5\ApplicationIbcbScopeDetail;
use App\Models\Section5\ApplicationIbcbScopeTis;
class ApplicationIbcbScope extends Model
{

    protected $table = 'section5_application_ibcb_scopes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'application_id',
        'application_no',
        'branch_group_id',
        'isic_no',
        'type',
        'remarks_reduce',
        'live_scope_id',
        'created_by',
        'updated_by',
        'ibcb_id',
        'ibcb_code'
    ];

    public function bs_branch_group(){
        return $this->belongsTo(BranchGroup::class, 'branch_group_id');
    }

    public function scopes_details(){
        return $this->hasMany(ApplicationIbcbScopeDetail::class, 'ibcb_scope_id');
    }

    public function scopes_tis(){
        return $this->hasMany(ApplicationIbcbScopeTis::class, 'ibcb_scope_id');
    }

    public function getBranchGroupTitleAttribute(){
        return @$this->bs_branch_group->title;
    }

    public function getScopeTypeTitleAttribute() {
        $type_arr = [ '1' => 'ขอบข่ายเดิม', '2' => 'ขอเพิ่มขอบข่าย', '3' => 'ขอลดขอบข่าย' ];
        return array_key_exists( $this->type,  $type_arr )?$type_arr[ $this->type ]:'-';
    }

    public function getScopeBranchsAttribute(){

        $app_scope = $this->scopes_details()->select('branch_id')->groupBy('branch_id')->get();
        $list = [];
        foreach( $app_scope AS $item ){
            $bs_branch = $item->bs_branch;

            if( !is_null($bs_branch) ){
                $list[] = $bs_branch->title;
            }

        }

        $txt = implode( ' ,',  $list );

        return $txt;
    }

    public function application_ibcb(){
        return $this->belongsTo(ApplicationIbcb::class, 'application_id');
    }

    public function getScopeTisNoListAttribute() {
        return $this->scopes_tis->pluck('tis_no')->filter()->implode(', ');
    }
}
