<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Basic\BranchGroup;
use App\Models\Basic\Branch;
use App\User;
class InspectorsScope extends Model
{
        /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_inspectors_scopes';

    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inspectors_id',
        'inspectors_code',
        'branch_id',
        'branch_group_id',
        'agency_taxid',
        'start_date',
        'end_date',
        'state',
        'ref_inspector_application_no',
        'created_by',
        'updated_by'
    ];

    public function bs_branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function bs_branch_group(){
        return $this->belongsTo(BranchGroup::class,  'branch_group_id');
    }

    public function ins_scopes_tis(){
        return $this->hasMany(InspectorsScopeTis::class, 'inspector_scope_id');
    }
    
}
