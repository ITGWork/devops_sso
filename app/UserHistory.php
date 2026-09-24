<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class UserHistory extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sso_users_historys';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'user_id',
                            'data_field',
                            'data_old',
                            'data_new',
                            'remark',
                            'created_at',
                            'created_by',
                            'editor_type'
                        ];

    public $timestamps = false;

    /* บันทึกข้อมูล */
    static function Add($user_id, $data_field, $data_old, $data_new, $remark=null, $editor_type='owner'){

        $history = new UserHistory;
        $history->user_id    = $user_id;
        $history->data_field = $data_field;
        $history->data_old   = $data_old;
        $history->data_new   = $data_new;
        $history->remark     = $remark;
        $history->created_by = null;
        $history->created_at = date('Y-m-d H:i:s');
        $history->editor_type = $editor_type;
        $history->save();

    }

}
