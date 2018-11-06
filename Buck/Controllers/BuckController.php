<?php

namespace Statamic\Addons\Buck\Controllers;

use Log;
use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;
use Statamic\API\Crypt;
use Statamic\API\Email;
use Statamic\API\Request;
use Statamic\Extend\Controller;
use Illuminate\Support\Facades\Cookie;
use Statamic\Addons\Buck\Models\Order;
use Statamic\Addons\Buck\Models\Product;
use Statamic\Addons\Buck\Models\Customer;
use Statamic\Addons\Buck\Models\OrderItem;
use Statamic\Addons\Buck\Exceptions\BuckException;

class BuckController extends Controller
{
    /**
     * The data with which to create an order.
     *
     * @var array
     */
    public $fields = [];

    /** @var Order */
    private $order;

    /** @var Customer */
    private $customer;

    public function init()
    {
        $this->loadData();

        // the order id will always be in the session and always have been created (by the tag)
        $this->order = Order::find(session('buck_order_id'));

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function getTest()
    {
    }

    public function postCart()
    {
        $this->order->items()->save($this->createItem());

        return $this->redirectOrBack(cookie(
            'buck_order_id',
            Crypt::encrypt($this->order->id),
            10080
        ));
    }

    public function getRemoveFromCart()
    {
        return $this->deleteCart();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCart($id)
    {
        OrderItem::find($id)->delete();

        return $this->redirectOrBack();
    }

    public function postCheckout()
    {
        try {
            // attempt to process payment
            // if successful, add email to order, mark completed, send receipt/acknowledgement of payment
            $charge = $this->completeOrder($this->charge());

            // send back success and what details???
            // process the payment & send details back
            $this->flash->put('details', $charge);
            $this->flash->put('success', true);

            // get rid of the order from the session & cookie
            session()->forget('buck_order_id');
            $cookie = Cookie::forget('buck_order_id');

            return $this->redirectOrBack($cookie);
        } catch (BuckException $e) {
            Log::error($e->getMessage());

            return back()->withInput()->withErrors($e->getMessage(), 'buck');
        }
    }

    /**
     * @param $details array
     *
     * @return array
     *
     * @throws \Statamic\Addons\Buck\BuckException
     */
    private function charge()
    {
        /*
         Check to see if they're an existing customer & see if they have a gateway_id that we can use. If they aren't an existing customer, create them w/ no password.

         if there is a gateway_id, use it, otherwise just send the source and store the
         gateway_id we get back from the processor

         if there's a source_id in the request use it

         @todo set their default source to the new one?

        */
        try {
            /** @var Customer $customer */
            $this->customer = Customer::findOrCreate($this->fields);

            $data = [
                'amount' => $this->order->total(),
                'currency' => 'usd',
                'statement_descriptor' => 'STORENAME - Order #' . $this->order->id,
            ];

            if ($this->customer->gateway_customer_id) {
                $data['customer'] = $this->customer->gateway_customer_id;
            }

            if (isset($this->fields['stripeSource'])) {
                $data['source'] = $this->fields['stripeSource'];
            }

            $charge = Charge::create($data);

            $this->customer->gateway_id = $charge->customer;

            return $charge->id;
        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];

            $message = $err['type'] . ' - ' . $err['code'] . ' - ' . $err['message'];

            Log::error($message);

            throw new BuckException($message);
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $body = $e->getJsonBody();
            $err = $body['error'];

            $message = $err['type'] . ' - ' . $err['message'];

            Log::error($message);

            throw new BuckException($message);
        }
    }

    // if successful, add email to order, mark completed, send receipt/acknowledgement of payment
    private function completeOrder($transaction_id)
    {
        // update status to completed
        // save customer to order
        $this->order->update(
            [
                'customer_id' => $this->customer->id,
                'gateway_transaction_id' => $transaction_id,
                'completed_at' => Carbon::now(),
            ]
        );

        $this->customer->save();

        // email receipt
        $this->sendReceipt();
    }

    private function sendReceipt()
    {
        Email::to($this->customer->email())
        ->from($this->getConfig('from_email'))
        ->in('site/themes/' . Config::getThemeName() . '/templates')
        ->template($this->getConfig('receipt_template'))
        ->with($this->order->toArray())
        ->send();
    }

    private function redirectOrBack($cookie = null)
    {
        $response = back();
        if ($redirect = request('redirect')) {
            $response = redirect($redirect);
        }

        return $cookie ? $response->withCookie($cookie) : $response;
    }

    /**
     * @return \Statamic\Addons\Buck\Models\OrderItem
     */
    private function createItem()
    {
        /** @var Product $product */
        $product = Product::find($this->fields['product_id']);

        return new OrderItem(
            [
                'product' => $product,
                'quantity' => $this->fields['quantity'],
                'price' => $product->price(),
            ]
        );
    }

    /**
     * Filter out any meta fields from the request object and
     * and assign them to class variables, leaving you with
     * a nice and clean $fields variable to work with.
     *
     * @return void
     */
    private function loadData()
    {
        $data = Request::has('_data') ? Crypt::decrypt(Request::get('_data')) : [];
        $this->fields = array_merge(Request::except('_token'), $data);
    }
}
