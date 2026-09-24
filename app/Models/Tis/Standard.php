<?php

namespace App\Models\Tis;

use App\Models\Basic\SetFormat;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tis_standards';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 'title',
                            'title_en',
                            'tis_force',
                            'issue_date',
                            'amount_date',
                            'gaz_date',
                            'gaz_no',
                            'gaz_space',
                            'tis_no',
                            'tis_year',
                            'tis_book',
                            'remark',
                            'board_type_id',
                            'standard_type_id',
                            'standard_format_id',
                            'set_format_id',
                            'method_id',
                            'method_id_detail',
                            'product_group_id',
                            'industry_target_id',
                            'staff_group_id',
                            'staff_responsible',
                            'refer',
                            'attach',
                            'state',
                            'review_status',
                            'ics',
                            'isbn',
                            'minis_dated',
                            'minis_dated_compulsory',
                            'issue_date_compulsory',
                            'minis_no_compulsory',
                            'gaz_date_compulsory',
                            'gaz_no_compulsory',
                            'gaz_space_compulsory',
                            'announce_compulsory',
                            'government_gazette',
                            'created_by',
                            'updated_by',
                            'minis_no',
                            'cancel_date',
                            'cancel_reason',
                            'cancel_minis_no',
                            'cancel_attach',
                            'amount_date_compulsory',
                            'set_std_id',
                            'tis_tisno'
                          ];

}
