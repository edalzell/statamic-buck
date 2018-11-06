<?php

namespace Statamic\Addons\Buck\SuggestModes;

use Statamic\Addons\Buck\Models\DiscountType;
use Statamic\Addons\Suggest\Modes\AbstractMode;

class DiscountTypesSuggestMode extends AbstractMode
{
    public function suggestions()
    {
        return DiscountType::all()
            ->map(function ($type, $key) {
                return ['value' => $type->id, 'text' => $type->title];
            })
            ->values()
            ->all();
    }
}
