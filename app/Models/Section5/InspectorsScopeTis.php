<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tis\Standard;

class InspectorsScopeTis extends Model
{
    protected $table = 'section5_inspectors_scope_tis';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inspector_scope_id',
        'inspectors_code',
        'tis_id',
        'tis_no',
        'tis_name',
        'state'
    ];

    public function scope_tis_std(){
        return $this->belongsTo(Standard::class,  'tis_id');
    }

    public function inspector_scope(){
        return $this->belongsTo(InspectorsScope::class, 'inspector_scope_id');
    }

    public function getScopeTisBranchGroupNameAttribute() {
        return $this->inspector_scope->ScopeBranchGroupName??'n/a';
    }

}
