<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Basic\BranchGroup;
use App\Models\Section5\ApplicationIbcbBoardApprove;
use App\Models\Section5\IbcbsScopeDetail;
use App\Models\Section5\IbcbsScopeTis;

class IbcbsScope extends Model
{
    protected $table = 'section5_ibcbs_scopes';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ibcb_id',
        'ibcb_code',
        'branch_group_id',
        'isic_no',
        'start_date',
        'end_date',
        'state',
        'ref_ibcb_application_no',
        'created_by',
        'updated_by',
        'type'

    ];

    public function bs_branch_group(){
        return $this->belongsTo(BranchGroup::class,  'branch_group_id');
    }

    public function getScopeBranchGroupNameAttribute() {
        return $this->bs_branch_group->title??'n/a';
    }

    public function getScopeBranchsAttribute() {
        return $this->scopes_details->map(function($detail){
            return $detail->bs_branch->title??null;
        })->filter()->implode(', ');
    }

    public function getScopeTisNoListAttribute() {
        return $this->scopes_tis->pluck('tis_no')->filter()->implode(', ');
    }

    public function application_ibcb_board_approve(){
        return $this->belongsTo(ApplicationIbcbBoardApprove::class, 'ref_ibcb_application_no', 'application_no'  );
    }

    public function scopes_details(){
        return $this->hasMany(IbcbsScopeDetail::class,  'ibcb_scope_id', 'id');
    }

    public function scopes_tis(){
        return $this->hasMany(IbcbsScopeTis::class,  'ibcb_scope_id', 'id');
    }

    public function ibcb(){
        return $this->belongsTo(Ibcbs::class, 'ibcb_id');
    }

}
