<?php
/**
 * Created by PhpStorm.
 * User: erin
 * Date: 2017-12-27
 * Time: 4:08 PM
 */

namespace Statamic\Addons\Buck\Models;

use Statamic\Data\Entries\Entry;
use Statamic\API\Entry as EntryAPI;

class Product extends Entry
{
    public static function find($id)
    {
        $product = new Product();

        return $product->data(EntryAPI::find($id)->data());
    }

    /**
     * @param int $price
     * @return int|Product
     */
    public function price($price = null)
    {
        if (is_null($price)) {
            return int($this->get('price'));
        }

        return $this->set('price', $price);
    }

    /**
     * @return float
     */
    public function priceInDollars()
    {
        return $this->price() / 100;
    }
}
