<?php

namespace Statamic\Addons\Buck\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountType extends Model
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
}
