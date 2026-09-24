<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Basic\Branch;

class ApplicationIbcbScopeDetail extends Model
{
    protected $table = 'section5_application_ibcb_scopes_details';

    protected $primaryKey = 'id';

    protected $fillable = [ 
        'ibcb_scope_id',
        'application_no',
        'branch_id',
        'ibcb_id', 
        'ibcb_code'
    ];

    public function bs_branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }  
}
