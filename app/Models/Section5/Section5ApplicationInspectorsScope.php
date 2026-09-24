<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\Models\Basic\BranchGroup;
use App\Models\Basic\Branch;
use App\Models\Section5\Section5ApplicationInspector;

class Section5ApplicationInspectorsScope extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_inspectors_scope';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'application_id',
                            'application_no',
                            'branch_id',
                            'branch_group_id',
                            'created_by',
                            'updated_by'
                        ];

    public function section5_application_inspectors(){
        return $this->belongsTo(Section5ApplicationInspector::class, 'application_id');
    }

    public function basic_branch_groups(){
        return $this->belongsTo(BranchGroup::class, 'branch_group_id');
    }

    public function basic_branches(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    //มอก.
    public function scopes_tis(){
        return $this->hasMany(Section5ApplicationInspectorsScopeTis::class, 'inspector_scope_id');
    }

    public function getBranchGroupNameAttribute() {
        return !empty($this->basic_branch_groups->title)?$this->basic_branch_groups->title:null;
    }

    public function getBranchNameAttribute() {
        return !empty($this->basic_branches->title)?$this->basic_branches->title:null;
    }

}
