<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiHouseRegis extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'api_house_regis';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'request_id',
                            'agent_id',
                            'citizenID',
                            'alleyCode',
                            'alleyDesc',
                            'AlleyWayCode',
                            'AlleyWayDesc',
                            'districtCode',
                            'districtDesc',
                            'houseID',
                            'houseNo',
                            'houseType',
                            'houseTypeDesc',
                            'provinceCode',
                            'provinceDesc',
                            'rcodeCode',
                            'rcodeDesc',
                            'roadCode',
                            'roadDesc',
                            'subdistrictCode',
                            'subdistrictDesc',
                            'villageNo',
                        ];


}
