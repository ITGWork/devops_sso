<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

class IbcbsInspectors extends Model
{
    protected $table = 'section5_ibcbs_inspectors';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'ibcb_id',
        'ibcb_code',
        'inspector_id',
        'inspector_prefix',
        'inspector_first_name',
        'inspector_last_name',
        'inspector_taxid',
        'inspector_type',
        'ref_ibcb_application_no',
        'type'
    ];

    public function getIspesTorFullNameAttribute() {
        return (!empty($this->inspector_prefix)?$this->inspector_prefix:null).($this->inspector_first_name).' '.($this->inspector_last_name);
    }
}
