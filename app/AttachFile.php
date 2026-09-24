<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Basic\AttachmentType;
use Storage;

class AttachFile extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attach_files';

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
    protected $fillable = ['tax_number', 'username', 'systems', 'ref_table', 'ref_id','url', 'filename', 'new_filename','section', 'setting_file_id', 'size', 'caption', 'file_properties', 'created_by', 'updated_by'];


}
