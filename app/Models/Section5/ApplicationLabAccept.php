<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Models\Officer\User;
use App\Models\Section5\ApplicationLabStatus;
use App\User As SSO_USER;

class ApplicationLabAccept extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'section5_application_labs_accept';

    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [ 
                            'application_lab_id',
                            'application_no',
                            'application_status',
                            'description',
                            'appointment_date',
                            'send_mail_status',
                            'noti_email',
                            'created_by'
                        ];

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_sso(){
        return $this->belongsTo(SSO_USER::class, 'created_by');
    }

    public function getNotiEmailsAttribute() {
        $noti_emails = !empty($this->noti_email)?json_decode($this->noti_email):[];
        $noti_emails = !empty($noti_emails)?implode(',', $noti_emails):null;
        return @$noti_emails;
    }

    // ผู้รับรับคำขอ
    public function getRequestRecipientAttribute(){
        if($this->appointment_date=='edit_page'){
            return $this->user_sso->name;
        }else{
            return $this->user_created->FullName;
        }
        
    }

    // วันที่รับคำขอครั้งแรก รูปแบบ 31/01/2565
    public function getDateOfFirstRequestAttribute(){
        $date = null;
        if(Carbon::hasFormat($this->created_at, 'Y-m-d H:i:s')){
            $date = Carbon::parse($this->created_at)->addYear(543)->format('d/m/Y');
        }
        return $date;
    }

    // วันที่รับคำขอครั้งแรก รูปแบบ 31 มกราคม 2565
    public function getDateOfFirstRequestFullAttribute(){
        $date = null;
        if(Carbon::hasFormat($this->created_at, 'Y-m-d H:i:s')){
            $date = Carbon::parse($this->created_at)->addYear(543)->isoFormat('DD MMMM YYYY');
        }
        return $date;
    }

    public function application_lab_status(){
        return $this->belongsTo(ApplicationLabStatus::class, 'application_status')->withDefault();
    }  

    public function getAppStatusAttribute(){
        $status = @$this->application_lab_status->title;
        if($this->application_status == 100 && !empty($this->remarks_delete)){
            $status .= "<br>({$this->remarks_delete})";
        }
        return $status;
    }

}
