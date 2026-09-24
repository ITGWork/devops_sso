<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiPersonals extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'api_personals';

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
                            'age',
                            'dateOfBirth',
                            'dateOfMoveIn',
                            'fatherName',
                            'fatherNationalityCode',
                            'fatherNationalityDesc',
                            'fatherPersonalID',
                            'firstName',
                            'fullnameAndRank',
                            'genderCode',
                            'genderDesc',
                            'lastName',
                            'middleName',
                            'motherName',
                            'motherNationalityCode',
                            'motherNationalityDesc',
                            'motherPersonalID',
                            'NationalityCode',
                            'NationalityDesc',
                            'ownerStatusDesc',
                            'statusOfPersonCode',
                            'statusOfPersonDesc',
                            'titleCode',
                            'titleDesc',
                            'titleName',
                            'titleSex',
                        ];


}
