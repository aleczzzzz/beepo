<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'status',
        'position'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function scopeGreatParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopePending($query)
    {
        return $query->whereStatus('pending');
    }

    public function scopeComplete($query)
    {
        return $query->whereStatus('complete');
    }

    public function scopeCancelled($query)
    {
        return $query->whereStatus('cancelled');
    }

    /**
     * Get the user that owns the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Get the parent that owns the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    /**
     * Get all of the children for the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id', 'id')->orderBy('position', 'asc')->with('children');
    }

    public function pendingChildren(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id', 'id')->pending()->orderBy('position', 'asc')->with('pendingChildren');
    }

    public function completeChildren(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id', 'id')->orderBy('position', 'asc')->with('children');
    }
}
