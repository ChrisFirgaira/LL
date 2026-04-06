<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Lunar\Facades\CartSession;
use Lunar\Models\Order;

class CheckoutController extends Controller
{
    public function index(): RedirectResponse|Response
    {
        $cart = CartSession::current();

        if (! $cart || ! $cart->lines->count()) {
            return to_route('cart.show')->with('error', 'Add items to your cart before checkout.');
        }

        return Inertia::render('Checkout/Index', [
            'countries' => StorefrontData::countries(),
            'checkout' => StorefrontData::checkout($cart),
        ]);
    }

    public function addresses(Request $request): RedirectResponse
    {
        $cart = CartSession::current();
        abort_if(! $cart || ! $cart->lines->count(), 404);

        $data = $request->validate([
            'same_as_billing' => ['required', 'boolean'],
            'billing' => ['required', 'array'],
            'billing.first_name' => ['required', 'string', 'max:100'],
            'billing.last_name' => ['nullable', 'string', 'max:100'],
            'billing.company_name' => ['nullable', 'string', 'max:150'],
            'billing.line_one' => ['required', 'string', 'max:255'],
            'billing.line_two' => ['nullable', 'string', 'max:255'],
            'billing.city' => ['required', 'string', 'max:150'],
            'billing.state' => ['nullable', 'string', 'max:150'],
            'billing.postcode' => ['required', 'string', 'max:30'],
            'billing.country_id' => ['required', 'integer'],
            'billing.contact_email' => ['required', 'email', 'max:255'],
            'billing.contact_phone' => ['nullable', 'string', 'max:50'],
            'shipping' => ['nullable', 'array'],
            'shipping.first_name' => ['nullable', 'string', 'max:100'],
            'shipping.last_name' => ['nullable', 'string', 'max:100'],
            'shipping.company_name' => ['nullable', 'string', 'max:150'],
            'shipping.line_one' => ['nullable', 'string', 'max:255'],
            'shipping.line_two' => ['nullable', 'string', 'max:255'],
            'shipping.city' => ['nullable', 'string', 'max:150'],
            'shipping.state' => ['nullable', 'string', 'max:150'],
            'shipping.postcode' => ['nullable', 'string', 'max:30'],
            'shipping.country_id' => ['nullable', 'integer'],
            'shipping.contact_email' => ['nullable', 'email', 'max:255'],
            'shipping.contact_phone' => ['nullable', 'string', 'max:50'],
            'shipping.delivery_instructions' => ['nullable', 'string', 'max:500'],
        ]);

        $billing = $this->normalizeAddress($data['billing']);
        $shipping = $data['same_as_billing']
            ? $billing
            : $this->normalizeAddress($data['shipping'] ?? []);

        $cart->setBillingAddress($billing);

        if ($cart->isShippable()) {
            $cart->setShippingAddress($shipping);
        }

        return to_route('checkout.index')->with('success', 'Addresses saved.');
    }

    public function shipping(Request $request): RedirectResponse
    {
        $cart = CartSession::current();
        abort_if(! $cart || ! $cart->lines->count(), 404);

        if (! $cart->isShippable()) {
            throw ValidationException::withMessages([
                'shipping_option' => 'This cart does not require a shipping method.',
            ]);
        }

        if (! $cart->shippingAddress) {
            throw ValidationException::withMessages([
                'shipping_option' => 'Please save your shipping address before choosing a shipping method.',
            ]);
        }

        $data = $request->validate([
            'shipping_option' => ['required', 'string'],
        ]);

        $option = CartSession::getShippingOptions()
            ->firstWhere('identifier', $data['shipping_option']);

        if (! $option) {
            throw ValidationException::withMessages([
                'shipping_option' => 'Please select a valid shipping method.',
            ]);
        }

        $cart->setShippingOption($option);

        return to_route('checkout.index')->with('success', 'Shipping method updated.');
    }

    public function place(Request $request): RedirectResponse
    {
        $cart = CartSession::current();
        abort_if(! $cart || ! $cart->lines->count(), 404);

        $request->validate([
            'accept_terms' => ['accepted'],
        ]);

        try {
            $order = $cart->createOrder();
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', 'Checkout is incomplete. Please review your details and try again.');
        }

        $order->forceFill([
            'placed_at' => now(),
            'status' => 'payment-offline',
        ])->save();

        CartSession::forget();
        $request->session()->put('checkout.last_order_id', $order->id);

        return to_route('checkout.success');
    }

    public function success(Request $request): RedirectResponse|Response
    {
        $orderId = $request->session()->get('checkout.last_order_id');

        if (! $orderId) {
            return to_route('home');
        }

        $order = Order::query()->findOrFail($orderId);

        return Inertia::render('Checkout/Success', [
            'order' => StorefrontData::order($order),
        ]);
    }

    protected function normalizeAddress(array $address): array
    {
        return [
            'title' => $address['title'] ?? null,
            'first_name' => $address['first_name'] ?? null,
            'last_name' => $address['last_name'] ?? null,
            'company_name' => $address['company_name'] ?? null,
            'line_one' => $address['line_one'] ?? null,
            'line_two' => $address['line_two'] ?? null,
            'city' => $address['city'] ?? null,
            'state' => $address['state'] ?? null,
            'postcode' => $address['postcode'] ?? null,
            'country_id' => $address['country_id'] ?? null,
            'contact_email' => $address['contact_email'] ?? null,
            'contact_phone' => $address['contact_phone'] ?? null,
            'delivery_instructions' => $address['delivery_instructions'] ?? null,
        ];
    }
}
