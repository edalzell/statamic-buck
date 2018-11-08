<?php

namespace Statamic\Addons\Buck\SuggestModes;

use Statamic\Addons\Suggest\Modes\AbstractMode;
use Statamic\Addons\Buck\Models\DiscountLimitType;

class DiscountLimitTypesSuggestMode extends AbstractMode
{
    public function suggestions()
    {
        return DiscountLimitType::all()
            ->map(function ($type, $key) {
                return ['value' => $type->id, 'text' => $type->title];
            })
            ->values()
            ->all();
    }
}
