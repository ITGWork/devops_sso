<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Officer\User;

class ApplicationInspectorsAccept extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_inspectors_accept';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
                            'application_id',
                            'application_no',
                            'application_status',
                            'description',
                            'send_mail_status',
                            'noti_email',
                            'created_by',
                            'updated_by'
                        ];

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // ผู้ดำเนินการ
    public function getRequestRecipientAttribute(){
        return $this->user_created->reg_fname.' '.$this->user_created->reg_lname;
    }


    public function section5_application_inspectors_status(){
        return $this->belongsTo(ApplicationInspectorStatus::class, 'application_status')->withDefault();
    }  

    public function getAppStatusAttribute(){
        $status = @$this->section5_application_inspectors_status->title;
        if($this->application_status == 11 && !empty($this->remarks_delete)){
            $status .= "<br>({$this->remarks_delete})";
        }
        return $status;
    }

}
