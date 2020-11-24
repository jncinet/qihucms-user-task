<?php

namespace Qihucms\UserTask\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Qihucms\UserTask\Events\TaskCompleted;

class UserTaskOrder extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_task_id', 'files', 'remark', 'status'
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => TaskCompleted::class
    ];

    /**
     * @var array
     */
    protected $casts = [
        'files' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsTo
     */
    public function user_task(): BelongsTo
    {
        return $this->belongsTo('Qihucms\UserTask\Models\UserTask');
    }
}