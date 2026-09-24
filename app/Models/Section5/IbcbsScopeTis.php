<?php

namespace App\Models\Section5;

use App\Models\Basic\Tis;
use Illuminate\Database\Eloquent\Model;

use App\Models\Tis\Standard;
use App\Models\Section5\IbcbsScopeDetail;
use App\Models\Section5\IbcbsScope;
use App\Models\Section5\Ibcbs;

class IbcbsScopeTis extends Model
{
    protected $table = 'section5_ibcbs_scopes_tis';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ibcb_scope_id',
        'ibcb_scope_detail_id',
        'tis_id',
        'tis_no',
        'ibcb_code',
        'type'
    ];

    public function scope_tis_std(){
        return $this->belongsTo(Tis::class, 'tis_id');
    }  

    public function scope_detail(){
        return $this->belongsTo(IbcbsScopeDetail::class,  'ibcb_scope_detail_id');
    } 

    public function ibcb_scope(){
        return $this->belongsTo(IbcbsScope::class,  'ibcb_scope_id');
    }  

    public function ibcb_data(){
        return $this->belongsTo(Ibcbs::class,  'ibcb_code','ibcb_code');
    }  

    public function getScopeTisBranchNameAttribute() {
        return $this->scope_detail->ScopeBranchName??'n/a';
    }

    public function getScopeTisBranchGroupNameAttribute() {
        return $this->ibcb_scope->ScopeBranchGroupName??'n/a';
    }

}
