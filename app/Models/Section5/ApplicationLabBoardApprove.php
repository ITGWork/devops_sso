<?php

namespace App\Models\Section5;

use Illuminate\Database\Eloquent\Model;
use App\AttachFile;

class ApplicationLabBoardApprove extends Model
{
    protected $table = 'section5_application_labs_board_approves';

    protected $primaryKey = 'id';

        /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_id',
        'application_no',
        'board_meeting_date',
        'board_meeting_result',
        'board_meeting_description',
        'government_gazette_date',
        'lab_start_date',
        'lab_end_date',
        'government_gazette_description',
        'created_by',
        'updated_by',
        'government_gazette_created_by',
        'government_gazette_updated_by',
        'government_gazette_created_at',
        'government_gazette_updated_at',
        'tisi_board_meeting_date',
        'tisi_board_meeting_result',
        'tisi_board_meeting_description',
    ];

    public function attach_file_gazette(){
        return $this->hasOne(AttachFile::class, 'ref_id', 'id')->where('section', 'file_attach_government_gazette')->where('ref_table', $this->getTable());
    }
}
