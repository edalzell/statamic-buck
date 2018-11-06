<?php

namespace Statamic\Addons\Buck\Models;

use Carbon\Carbon;
use Statamic\Extend\Extensible;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Extensible;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'completed_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_abandoned' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'completed_at',
        'customer_id',
        'discount_id',
        'discount',
        'gateway_transaction_id',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['items'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('Statamic\Addons\Buck\Models\Customer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function discount()
    {
        return $this->hasOne('Statamic\Addons\Buck\Models\DiscountType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('Statamic\Addons\Buck\Models\OrderItem');
    }

    public function total()
    {
        return $this->items->sum('total') - $this->discountAmount;
    }

    /**
     * Scope a query to only include abandoned orders.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbandoned($query)
    {
        return $query
            ->whereNull('completed_at')
            ->whereDate(
                'updated_at',
                '<',
                Carbon::now()->subDays($this->getConfig('abandoned_delay', 7))
            );
    }
}
