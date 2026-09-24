<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Basic\BranchGroup;
use App\Models\Basic\Branch;
class ApplicationIbcbInspectorsScope extends Model
{
    protected $table = 'section5_application_ibcb_inspectors_scopes';

    protected $primaryKey = 'id';

    protected $fillable = [ 

        'ibcb_inspector_id',
        'application_no',
        'branch_group_id',
        'branch_id'
        
    ];

    public function bs_branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }  
    
    public function bs_branch_group(){
        return $this->belongsTo(BranchGroup::class,  'branch_group_id');
    }  
}
