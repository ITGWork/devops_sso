<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;

class MoiProg extends Model
{
    protected $table = 'moi_prog';
    protected $primaryKey = 'progid';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['progid', 'prog_name', 'prog_url', 'status'];
}
