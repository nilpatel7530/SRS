<?php

namespace Modules\Projects\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProjectActivity extends Model
{
    protected $table = 'project_activity_logs';

    protected $fillable = [
        'project_id',
        'user_id',
        'type',
        'action',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
