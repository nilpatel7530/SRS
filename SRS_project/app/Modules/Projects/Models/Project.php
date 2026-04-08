<?php

namespace Modules\Projects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Modules\Finance\Models\CapexEntry;
use Modules\Finance\Models\OpexEntry;
use Modules\Finance\Models\BankGuarantee;
use Modules\Finance\Models\Invoice;
use Modules\Documents\Models\Document;

class Project extends Model
{
    protected $fillable = ['name', 'department_id', 'type'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function capexEntries(): HasMany
    {
        return $this->hasMany(CapexEntry::class);
    }

    public function opexEntries(): HasMany
    {
        return $this->hasMany(OpexEntry::class);
    }

    public function bankGuarantees(): HasMany
    {
        return $this->hasMany(BankGuarantee::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
