<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiJuristics extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'api_juristics';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'request_id',
                            'agent_id',
                            'juristicType',
                            'juristicID',
                            'oldJuristicID',
                            'registerDate',
                            'juristicName_TH',
                            'juristicName_EN',
                            'registerCapital',
                            'paidRegisterCapital',
                            'numberOfObjective',
                            'numberOfPageOfObjective',
                            'juristicStatus',
                            'standardID',
                            'authorizeDescriptions',
                            'standardObjectives',
                            'rcodeDesc',
                            'roadCode',
                            'roadDesc',
                            'subdistrictCode',
                            'addressInformations',
                        ];


}
