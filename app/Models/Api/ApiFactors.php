<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ApiFactors extends Model
{
    use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'api_factors';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'request_id',
                            'agent_id',
                            'ampName',
                            'cap',
                            'colonyIndustDesc',
                            'dispFacReg',
                            'expDate',
                            'facType',
                            'fAddr',
                            'fFlag',
                            'fID',
                            'fMoo',
                            'fName',
                            'HP',
                            'IndustType',
                            'Object',
                            'OName',
                            'ProvName',
                            'Road',
                            'Soi',
                            'StartDate',
                            'Trade',
                            'TumName',
                            'Works',
                            'ZipCode',
                            'ZoneDesc',
                        ];


}
