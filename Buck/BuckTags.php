<?php

namespace Statamic\Addons\Buck;

use Statamic\API\Crypt;
use Statamic\Extend\Tags;
use Stringy\StaticStringy as Stringy;
use Statamic\Addons\Buck\Models\Order;
use Statamic\Addons\Buck\Models\Product;

class BuckTags extends Tags
{
    /**
     * Fields that can be overridden with tag parameters
     *
     * @var array
     */
    private $meta = [
        'product_id',
    ];

    private $order;

    /**
     * The middleman. The camelCase handler. The dude.
     * We are using buck:noun:verb syntax, and
     * this does the magic transformation.
     *
     * @param string $method
     * @param array  $args
     * @return method
     */
    public function __call($method, $args)
    {
        $method = Stringy::camelize(str_replace(':', '_', $method));

        if ($this->isAllowed() && method_exists($this, $method)) {
            return $this->$method();
        }
    }

    protected function init()
    {
        // if there's one in the session, use it
        $id = session('buck_order_id');

        // otherwise see if it's in the cookie
        if (!$id && request()->hasCookie('buck_order_id')) {
            $id = Crypt::decrypt(request()->cookie('buck_order_id'));
        }

        // get it or create it, whatever man
        if (!$id || !($this->order = Order::find($id))) {
            $this->order = Order::create();
        }

        $this->meta['order_id'] = $this->order->id;

        // put it in the session so we don't make a zillion orders
        session(['buck_order_id' => $this->order->id]);
    }

    private function cartShow()
    {
        return $this->parse($this->order->toArray());
    }

    /**
     * The {{ buck:cart:add }} tag
     *
     * @return string|array
     */
    public function cartAdd()
    {
        $data = array_merge(
            $this->getErrorsAndSuccess(),
            Product::find($this->get('product_id'))->data()
        );

        $html = $this->formOpen('/cart/');
        $html .= $this->getMetaFields();
        $html .= $this->parse($data);
        $html .= '</form>';

        return $html;
    }

    public function cartRemoveItem()
    {
        $html = $this->formOpen('/cart/' . $this->getParam('id'));
        $html .= method_field('delete');
        $html .= $this->parse([]) . '</form>';

        return $html;
    }

    public function paymentForm()
    {
        $html = $this->formOpen('/checkout/');

        if ($redirect = $this->getRedirectUrl()) {
            $html .= '<input type="hidden" name="redirect" value="' . $redirect . '" />';
        }

        $html .= $this->parse([]) . '</form>';

        return $html;
    }

    /**
     * The {{ buck:js }} tag
     *
     * @return string
     */
    public function js()
    {
        $js = '<script src="https://js.stripe.com/v3/"></script>' . PHP_EOL;
        $js .= $this->js->inline("var stripe = Stripe('" . env('STRIPE_PUBLIC_KEY') . "')") . PHP_EOL;
        $js .= $this->js->inline('var elements = stripe.elements()') . PHP_EOL;
        $js .= $this->js->tag('buck') . PHP_EOL;

        return $js;
    }

    /**
     * Encrypts any special meta fields set as tag parameters
     * and sets them in a special HTML hidden input field.
     *
     * @return string
     */
    private function getMetaFields()
    {
        $meta = array_intersect_key($this->parameters, array_flip($this->meta));

        if ($meta) {
            return '<input type="hidden" name="_data" value="' . Crypt::encrypt($meta) . '" />';
        }
    }

    /**
     * Get any errors or success messages ready to parse
     *
     * @return array
     */
    private function getErrorsAndSuccess()
    {
        $data = [];

        if ($this->hasErrors()) {
            $data['errors'] = session('errors')->getBag('buck')->all();
        }

        if ($this->flash->exists('success')) {
            $data['success'] = true;
        }

        return $data;
    }

    /**
     * Does this form have errors?
     *
     * @return bool
     */
    private function hasErrors()
    {
        return (session()->has('errors'))
            ? session()->get('errors')->hasBag('buck')
            : false;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    private function isAllowed()
    {
        return true;
    }

    private function createForm($action, $data = [])
    {
        if ($this->success()) {
            $data['success'] = true;
            $data['details'] = $this->flash->get('details');
        }

        if ($this->hasErrors()) {
            $data['errors'] = $this->getErrorBag()->all();
        }

        $html = $this->formOpen($action);

        if ($redirect = $this->getRedirectUrl()) {
            $html .= '<input type="hidden" name="redirect" value="' . $redirect . '" />';
        }

        return $html . $this->data() . $this->parse($data) . '</form>';
    }

    /**
     * Get the redirect URL
     *
     * @return string
     **/
    private function getRedirectUrl()
    {
        $return = $this->get('redirect');

        if ($this->getBool('allow_request_redirect')) {
            $return = request('redirect', $return);
        }

        return $return;
    }

    /**
     * Maps to {{ buck:success }}
     *
     * @return bool
     **/
    public function success()
    {
        return $this->flash->exists('success');
    }

    /**
     * Maps to {{ buck:errors }}
     *
     * @return bool|string
     **/
    public function errors()
    {
        if (!$this->hasErrors()) {
            return false;
        }

        $errors = [];

        foreach (session('errors')->getBag('charge')->all() as $error) {
            $errors[]['value'] = $error;
        }

        return ($this->content === '')    // If this is a single tag...
            ? !empty($errors)             // just output a boolean.
            : $this->parseLoop($errors);  // Otherwise, parse the content loop.
    }
}
