<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\AttachFile;

class ConfigsFaqs extends Model
{
    use Sortable;

    /**
     * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'configs_faqs';

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
                            'title',
                            'description',
                            'state',
                            'created_by',
                            'updated_by'
                        ];
  
    public function attach_file_faqs()
    {
        return $this->hasMany(AttachFile::class,'ref_id','id')->where('ref_table',$this->getTable())->where('section','file_faqs_configs');
    }
}

