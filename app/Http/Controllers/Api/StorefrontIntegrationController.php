<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Storefront\StorefrontData;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lunar\Exceptions\Carts\CartException;
use Lunar\Facades\ShippingManifest;
use Lunar\FieldTypes\Text;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Customer;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use RuntimeException;

class StorefrontIntegrationController extends Controller
{
    public function storeProduct(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:64'],
            'brand_id' => ['nullable', 'integer', 'exists:lunar_brands,id'],
            'product_type_id' => ['nullable', 'integer', 'exists:lunar_product_types,id'],
            'channel_id' => ['nullable', 'integer', 'exists:lunar_channels,id'],
            'sku' => ['required', 'string', 'max:120'],
            'tax_class_id' => ['nullable', 'integer', 'exists:lunar_tax_classes,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'currency_code' => ['nullable', 'string', 'max:10', 'exists:lunar_currencies,code'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'backorder' => ['nullable', 'integer', 'min:0'],
            'shippable' => ['nullable', 'boolean'],
            'purchasable' => ['nullable', 'in:always,in_stock,backorder'],
        ]);

        $productType = isset($data['product_type_id'])
            ? ProductType::query()->find($data['product_type_id'])
            : ProductType::query()->first();
        $channel = isset($data['channel_id'])
            ? Channel::query()->find($data['channel_id'])
            : Channel::getDefault();
        $taxClass = isset($data['tax_class_id'])
            ? TaxClass::query()->find($data['tax_class_id'])
            : TaxClass::getDefault();
        $currency = isset($data['currency_code'])
            ? Currency::query()->where('code', $data['currency_code'])->first()
            : Currency::getDefault();

        if (! $productType || ! $taxClass || ! $currency || ! $channel) {
            return response()->json([
                'message' => 'Missing defaults. Ensure product type, channel, currency and tax class exist.',
            ], 422);
        }

        $product = DB::transaction(function () use ($data, $productType, $taxClass, $currency, $channel) {
            $product = Product::query()->create([
                'product_type_id' => $productType->id,
                'status' => $data['status'] ?? 'published',
                'brand_id' => $data['brand_id'] ?? null,
                'attribute_data' => collect([
                    'name' => new Text($data['name']),
                    'description' => new Text($data['description'] ?? ''),
                ]),
            ]);

            $product->channels()->syncWithoutDetaching([$channel->id]);

            $variant = ProductVariant::query()->create([
                'product_id' => $product->id,
                'tax_class_id' => $taxClass->id,
                'sku' => $data['sku'],
                'stock' => $data['stock'] ?? 0,
                'backorder' => $data['backorder'] ?? 0,
                'shippable' => $data['shippable'] ?? true,
                'purchasable' => $data['purchasable'] ?? 'always',
                'unit_quantity' => 1,
            ]);

            Price::query()->create([
                'currency_id' => $currency->id,
                'priceable_type' => ProductVariant::morphName(),
                'priceable_id' => $variant->id,
                'price' => $this->toMinorUnits((float) $data['price'], $currency->decimal_places),
                'compare_price' => isset($data['compare_price'])
                    ? $this->toMinorUnits((float) $data['compare_price'], $currency->decimal_places)
                    : null,
                'min_quantity' => 1,
            ]);

            return $product->fresh()->load('variants.prices.currency');
        });

        $variant = $product->variants->first();
        $price = $variant?->prices->sortBy('min_quantity')->first();

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => [
                'product_id' => $product->id,
                'name' => $product->translateAttribute('name'),
                'status' => $product->status,
                'variant_id' => $variant?->id,
                'sku' => $variant?->sku,
                'price' => $price?->price?->formatted,
                'currency' => $price?->currency?->code,
            ],
        ], 201);
    }

    public function storeCustomer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:40'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'tax_identifier' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'address' => ['nullable', 'array'],
            'address.line_one' => ['required_with:address', 'string', 'max:255'],
            'address.line_two' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required_with:address', 'string', 'max:150'],
            'address.state' => ['nullable', 'string', 'max:150'],
            'address.postcode' => ['nullable', 'string', 'max:50'],
            'address.country_id' => ['required_with:address', 'integer', 'exists:lunar_countries,id'],
            'address.contact_email' => ['nullable', 'email', 'max:255'],
            'address.contact_phone' => ['nullable', 'string', 'max:50'],
            'address.shipping_default' => ['nullable', 'boolean'],
            'address.billing_default' => ['nullable', 'boolean'],
        ]);

        $customer = DB::transaction(function () use ($data) {
            $customer = Customer::query()->create([
                'title' => $data['title'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'company_name' => $data['company_name'] ?? null,
                'tax_identifier' => $data['tax_identifier'] ?? null,
            ]);

            $user = null;
            if (! empty($data['email'])) {
                $user = User::query()->where('email', $data['email'])->first();

                if (! $user) {
                    $user = User::query()->create([
                        'name' => trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')),
                        'email' => $data['email'],
                        'password' => $data['password'] ?? bin2hex(random_bytes(8)),
                    ]);
                }

                if (! $user->customers()->where('customer_id', $customer->id)->exists()) {
                    $user->customers()->attach($customer->id);
                }
            }

            if (! empty($data['address'])) {
                $address = $data['address'];
                $customer->addresses()->create([
                    'title' => $data['title'] ?? null,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'company_name' => $data['company_name'] ?? null,
                    'line_one' => $address['line_one'],
                    'line_two' => $address['line_two'] ?? null,
                    'city' => $address['city'],
                    'state' => $address['state'] ?? null,
                    'postcode' => $address['postcode'] ?? null,
                    'country_id' => $address['country_id'],
                    'contact_mail' => $address['contact_email'] ?? ($data['email'] ?? null),
                    'contact_phone' => $address['contact_phone'] ?? null,
                    'shipping_default' => $address['shipping_default'] ?? true,
                    'billing_default' => $address['billing_default'] ?? true,
                ]);
            }

            return $customer->fresh()->load(['users:id,email', 'addresses.country']);
        });

        return response()->json([
            'message' => 'Customer created successfully.',
            'data' => [
                'customer_id' => $customer->id,
                'name' => trim($customer->first_name.' '.$customer->last_name),
                'email' => $customer->users->first()?->email,
                'addresses_count' => $customer->addresses->count(),
            ],
        ], 201);
    }

    public function storeOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:lunar_customers,id'],
            'channel_id' => ['nullable', 'integer', 'exists:lunar_channels,id'],
            'currency_code' => ['nullable', 'string', 'max:10', 'exists:lunar_currencies,code'],
            'status' => ['nullable', 'string', 'max:64'],
            'placed_at' => ['nullable', 'date'],
            'meta' => ['nullable', 'array'],
            'same_as_billing' => ['nullable', 'boolean'],
            'billing' => ['required', 'array'],
            'billing.first_name' => ['required', 'string', 'max:100'],
            'billing.last_name' => ['nullable', 'string', 'max:100'],
            'billing.company_name' => ['nullable', 'string', 'max:150'],
            'billing.line_one' => ['required', 'string', 'max:255'],
            'billing.line_two' => ['nullable', 'string', 'max:255'],
            'billing.city' => ['required', 'string', 'max:150'],
            'billing.state' => ['nullable', 'string', 'max:150'],
            'billing.postcode' => ['required', 'string', 'max:30'],
            'billing.country_id' => ['required', 'integer', 'exists:lunar_countries,id'],
            'billing.contact_email' => ['nullable', 'email', 'max:255'],
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
            'shipping.country_id' => ['nullable', 'integer', 'exists:lunar_countries,id'],
            'shipping.contact_email' => ['nullable', 'email', 'max:255'],
            'shipping.contact_phone' => ['nullable', 'string', 'max:50'],
            'shipping.delivery_instructions' => ['nullable', 'string', 'max:500'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.variant_id' => ['required', 'integer', 'exists:lunar_product_variants,id'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.meta' => ['nullable', 'array'],
        ]);

        $customer = Customer::query()->findOrFail((int) $data['customer_id']);
        $channel = isset($data['channel_id'])
            ? Channel::query()->find($data['channel_id'])
            : Channel::getDefault();
        $currency = isset($data['currency_code'])
            ? Currency::query()->where('code', $data['currency_code'])->first()
            : Currency::getDefault();

        if (! $channel || ! $currency) {
            return response()->json([
                'message' => 'Missing defaults. Ensure channel and currency exist.',
            ], 422);
        }

        $sameAsBilling = (bool) ($data['same_as_billing'] ?? true);
        if (! $sameAsBilling && empty($data['shipping'])) {
            return response()->json([
                'message' => 'Shipping address is required when same_as_billing is false.',
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($data, $customer, $channel, $currency, $sameAsBilling) {
                $cart = Cart::query()->create([
                    'customer_id' => $customer->id,
                    'user_id' => $customer->users()->value('users.id'),
                    'channel_id' => $channel->id,
                    'currency_id' => $currency->id,
                ]);

                foreach ($data['lines'] as $line) {
                    $variant = ProductVariant::query()->findOrFail((int) $line['variant_id']);
                    $cart->add(
                        purchasable: $variant,
                        quantity: (int) $line['quantity'],
                        meta: (array) ($line['meta'] ?? []),
                        refresh: false
                    );
                }

                $cart = $cart->refresh()->recalculate();
                $cart->setBillingAddress($this->normalizeAddress($data['billing']));
                $cart->setShippingAddress(
                    $sameAsBilling
                        ? $this->normalizeAddress($data['billing'])
                        : $this->normalizeAddress($data['shipping'] ?? [])
                );

                if ($cart->isShippable()) {
                    $option = ShippingManifest::getOptions($cart)->first();
                    if (! $option) {
                        throw new RuntimeException('No valid shipping method is currently available for this order.');
                    }
                    $cart->setShippingOption($option);
                }

                $order = $cart->createOrder();
                $order->forceFill([
                    'status' => $data['status'] ?? 'payment-offline',
                    'placed_at' => isset($data['placed_at']) ? Carbon::parse($data['placed_at']) : now(),
                    'meta' => array_merge((array) $order->meta, (array) ($data['meta'] ?? [])),
                ])->save();

                return $order->fresh();
            });
        } catch (CartException $exception) {
            return response()->json([
                'message' => 'Order creation failed.',
                'errors' => $exception->errors()?->toArray() ?? ['order' => [$exception->getMessage()]],
            ], 422);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'Order creation failed.',
                'errors' => [
                    'shipping_option' => [$exception->getMessage()],
                ],
            ], 422);
        }

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => StorefrontData::order($order),
        ], 201);
    }

    private function normalizeAddress(array $address): array
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

    private function toMinorUnits(float $amount, int $decimalPlaces): int
    {
        $factor = $decimalPlaces > 0 ? (10 ** $decimalPlaces) : 1;

        return (int) round($amount * $factor);
    }
}
