<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Basic\Branch;
use App\Models\Section5\IbcbsScopeTis;

class IbcbsScopeDetail extends Model
{
    protected $table = 'section5_ibcbs_scopes_details';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'ibcb_id',
        'ibcb_code',
        'ibcb_scope_id',
        'branch_id',
        'audit_result',
        'type'
    ];

    public function bs_branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }  

    public function getScopeBranchNameAttribute() {
        return $this->bs_branch->title??'n/a';
    }

    public function scopes_tis(){
        return $this->hasMany(IbcbsScopeTis::class,  'ibcb_scope_detail_id', 'id');
    }  
    
}
