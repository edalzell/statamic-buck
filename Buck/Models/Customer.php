<?php

namespace Statamic\Addons\Buck\Models;

use Statamic\Data\Users\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
    ];

    public static function findOrCreate($data)
    {
        return static::where('email', $data['email'])->first() ?: static::create($data);
    }

    public function orders()
    {
        return $this->hasMany('Statamic\Addons\Buck\Models\Order');
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
