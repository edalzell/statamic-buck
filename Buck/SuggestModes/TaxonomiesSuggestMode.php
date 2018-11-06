<?php

namespace Statamic\Addons\Buck\SuggestModes;

use Statamic\API\Taxonomy;
use Statamic\Addons\Suggest\Modes\AbstractMode;

class TaxonomiesSuggestMode extends AbstractMode
{
    public function suggestions()
    {
        return Taxonomy::all()
            ->map(function ($taxonomy, $key) {
                return ['value' => $taxonomy->path(), 'text' => $taxonomy->title()];
            })
            ->values()
            ->all();
    }
}
