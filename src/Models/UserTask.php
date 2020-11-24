<?php

namespace Qihucms\UserTask\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Qihucms\UserTask\Events\TaskSaved;

class UserTask extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'thumbnail', 'start_time', 'end_time', 'stock',
        'currency_type_id', 'amount', 'content', 'btn_text', 'link', 'pay_status', 'status'
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => TaskSaved::class
    ];

    /**
     * @var array
     */
    protected $casts = [
        'stock' => 'integer',
        'amount' => 'decimal:2',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return HasMany
     */
    public function user_task_orders(): HasMany
    {
        return $this->hasMany('Qihucms\UserTask\Models\UserTaskOrder');
    }

    /**
     * @return BelongsTo
     */
    public function currency_type(): BelongsTo
    {
        return $this->belongsTo('Qihucms\Currency\Models\CurrencyType');
    }
}