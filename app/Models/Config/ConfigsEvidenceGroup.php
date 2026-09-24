<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Config\ConfigsEvidence;
use App\User;
use App\Models\Config\ConfigsEvidenceSystem;
class ConfigsEvidenceGroup extends Model
{
    use Sortable;

    /**
     * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'configs_evidence_groups';

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
    protected $fillable = [
                            'system',
                            'code',
                            'title',
                            'short_title',
                            'remarks',
                            'ordering',
                            'state',
                            'url',
                            'created_by',
                            'updated_by',
                            'created_at',
                            'updated_at'
                        ];

    public function SettingEvidenceData()
    {
        return $this->hasMany(ConfigsEvidence::class,'evidence_group_id');
    }

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

    /* Btn Switch Input*/
    public function getStateIconAttribute(){

        $btn = '';
        if ($this->state == 1) {
            $btn = '<div class="checkbox"><input class="js-switch" name="state" type="checkbox" value="'.$this->id.'" checked></div>';
        }else {
            $btn = '<div class="checkbox"><input class="js-switch" name="state" type="checkbox" value="'.$this->id.'"></div>';
        }

        return $btn;

  	}

    public function evidence_system(){
      return $this->belongsTo(ConfigsEvidenceSystem::class, 'system');
    }

    public function getEvidenceSystemNameAttribute() {
        return @$this->evidence_system->title;
    }

    public function getAttachmentNameAttribute() {

        $attachment = $this->SettingEvidenceData;

        $txt = '';
        foreach(  $attachment as $k => $item ){

            if( $k <= 4 ){
                $txt .= '<div>'.($item->title).'</div>';
            }
            
        }

        return @$txt;
    }
      
      
}
