<?php
namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;

use App\Models\Section5\Section5ApplicationInspector;
use App\Models\Tis\Standard;

class Section5ApplicationInspectorsScopeTis extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_inspectors_scope_tis';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'inspector_scope_id',
                            'application_no',
                            'tis_id',
                            'tis_no',
                            'tis_name'
                        ];

    public function application_inspector(){
        return $this->belongsTo(Section5ApplicationInspector::class, 'application_no', 'application_no');
    }

    public function application_inspector_scope(){
        return $this->belongsTo(Section5ApplicationInspectorsScope::class, 'inspector_scope_id');
    }

    public function standard(){
        return $this->belongsTo(Standard::class, 'tis_id');
    }

}
