<?php

namespace Statamic\Addons\Buck\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLink extends Model
{
    protected $dates = ['expires_at'];

    public function item()
    {
        return $this->belongsTo('Statamic\Addons\Buck\Models\OrderItem');
    }

    public function isExpired()
    {
        return $this->expired_at->isPast();
    }
}
