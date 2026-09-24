<?php

namespace App\Models\Agents;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\SettingSystem;
class AgentSystem extends Model
{
    protected $table = 'sso_agent_systems';

    protected $primaryKey = 'id';

    protected $fillable = ['setting_systems_id', 'sso_agent_id'];

    public function setting_system(){
        return $this->belongsTo(SettingSystem::class, 'setting_systems_id');
    }
    
}
