<?php

namespace Statamic\Addons\Buck\Models;

use Statamic\Extend\Extensible;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use Extensible;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['items'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->hasOne('Statamic\Addons\Buck\Models\DiscountType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function limitType()
    {
        return $this->hasOne('Statamic\Addons\Buck\Models\DiscountLimitType');
    }
}
