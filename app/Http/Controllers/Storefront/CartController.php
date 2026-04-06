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
use Lunar\Models\ProductVariant;

class CartController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Cart/Show', [
            'cart' => StorefrontData::cart(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $variant = ProductVariant::query()
            ->with(['product.defaultUrl', 'values', 'prices.currency'])
            ->findOrFail($data['variant_id']);

        $cart = CartSession::manager();

        try {
            $cart->add($variant, $data['quantity'] ?? 1);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', 'This item could not be added to the cart.');
        }

        return to_route('cart.show')->with('success', 'Item added to cart.');
    }

    public function update(Request $request, int $line): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $cart = CartSession::current();
        abort_if(! $cart || ! $cart->lines()->whereKey($line)->exists(), 404);

        try {
            $cart->updateLine($line, $data['quantity']);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('error', 'This cart line could not be updated.');
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(int $line): RedirectResponse
    {
        $cart = CartSession::current();
        abort_if(! $cart || ! $cart->lines()->whereKey($line)->exists(), 404);

        $cart->remove($line);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $cart = CartSession::current();

        if ($cart) {
            $cart->clear();
        }

        return back()->with('success', 'Cart cleared.');
    }
}
