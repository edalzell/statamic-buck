<?php
/**
 * Created by PhpStorm.
 * User: erin
 * Date: 2017-12-27
 * Time: 4:08 PM
 */

namespace Statamic\Addons\Buck\Models;

use Statamic\API\Term;
use Statamic\Extend\Extensible;
use Statamic\Data\Entries\Entry;
use Statamic\API\Entry as EntryAPI;

class Product extends Entry
{
    use Extensible;

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
        $price_field = $this->getConfig('product_price_field', 'price');

        if (is_null($price)) {
            return int($this->get($price_field));
        }

        return $this->set($price_field, $price);
    }

    /**
     * @return float
     */
    public function priceInDollars()
    {
        return $this->price() / 100;
    }

    /**
     * Does the product have a specific VAT value
     *
     * @return bool
     */
    public function hasVAT()
    {
        return $this->has($this->getConfig('vat_taxonomy'));
    }

    public function vatRate()
    {
        $vatTaxonomy = $this->getConfig('vat_taxonomy');

        return Term::whereSlug($vatTaxonomy, $this->get($vatTaxonomy))
            ->get($this->getConfig('vat_tax_field'), 0);
    }
}
