<?php

namespace Statamic\Addons\Buck\Models;

use Statamic\Data\Entries\Entry;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['order'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['product'];

    public function order()
    {
        return $this->belongsTo('Statamic\Addons\Buck\Models\Order');
    }

    /**
     * @param Entry $entry
     */
    public function setProductAttribute($entry)
    {
        $this->product_id = $entry->id();
    }

    public function getProductAttribute()
    {
        return Product::find($this->product_id)->data();
    }

    public function getTotalAttribute()
    {
        return $this->quantity * $this->price;
    }
}
