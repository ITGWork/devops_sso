<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\User;

class LogsSendMail extends Model
{
    protected $table = 'logs_send_mails';

    protected $primaryKey = 'id';

    protected $fillable = [ 'title', 'subject', 'learn', 'email', 'content', 'url_send', 'tb_ref', 'id_ref', 'system_code', 'site_code', 'created_by', 'updated_by' ];

    /*
      Sorting
    */
    public $sortable = [ 'title', 'subject', 'learn', 'email', 'content', 'url_send', 'tb_ref', 'id_ref', 'system_code', 'site_code', 'created_by', 'updated_by' ];

    /*
      User Relation
    */
    public function user_created(){
      return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated(){
      return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCreatedNameAttribute() {
  		return @$this->user_created->reg_fname.' '.@$this->user_created->reg_lname;
  	}

    public function getUpdatedNameAttribute() {
  		return @$this->user_updated->reg_fname.' '.@$this->user_updated->reg_lname;
  	}
}
