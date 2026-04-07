<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;

class AccountController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        $customerIds = $user?->customers()->pluck('lunar_customers.id') ?? collect();

        $orders = [];
        $pagination = null;
        $history = [
            'orderCount' => 0,
            'lifetimeSpend' => '-',
            'averageOrderValue' => '-',
            'itemsOrdered' => 0,
            'shipmentSummary' => [
                'pending' => 0,
                'shipped' => 0,
                'delivered' => 0,
                'cancelled' => 0,
            ],
        ];

        if ($user) {
            $orderScope = Order::query()
                ->where(function (Builder $query) use ($user, $customerIds) {
                    $query->where('user_id', $user->id);

                    if ($customerIds->isNotEmpty()) {
                        $query->orWhereIn('customer_id', $customerIds);
                    }
                });

            $aggregate = (clone $orderScope)
                ->selectRaw('COUNT(*) as order_count, COALESCE(SUM(total), 0) as spend_total')
                ->first();

            $orderCount = (int) ($aggregate->order_count ?? 0);
            $spendTotal = (int) ($aggregate->spend_total ?? 0);
            $averageOrderValue = $orderCount > 0 ? (int) round($spendTotal / $orderCount) : 0;

            $itemsOrdered = (int) DB::table((new OrderLine())->getTable())
                ->whereIn('order_id', (clone $orderScope)->select('id'))
                ->sum('quantity');

            $shipmentSummary = [
                'pending' => 0,
                'shipped' => 0,
                'delivered' => 0,
                'cancelled' => 0,
            ];

            $statusCounts = (clone $orderScope)
                ->selectRaw('status, COUNT(*) as aggregate_count')
                ->groupBy('status')
                ->pluck('aggregate_count', 'status');

            foreach ($statusCounts as $status => $count) {
                $bucket = $this->shipmentBucketFromStatus((string) $status);
                $shipmentSummary[$bucket] += (int) $count;
            }

            $history = [
                'orderCount' => $orderCount,
                'lifetimeSpend' => '$'.number_format($spendTotal / 100, 2),
                'averageOrderValue' => $orderCount > 0 ? '$'.number_format($averageOrderValue / 100, 2) : '-',
                'itemsOrdered' => $itemsOrdered,
                'shipmentSummary' => $shipmentSummary,
            ];

            $ordersPaginator = (clone $orderScope)
                ->with([
                    'productLines.purchasable.product',
                ])
                ->withCount('productLines')
                ->orderByDesc('placed_at')
                ->orderByDesc('created_at')
                ->paginate(25)
                ->withQueryString();

            $orders = $ordersPaginator->getCollection()
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'reference' => $order->reference ?: "#{$order->id}",
                    'status' => $order->status_label ?: $order->status,
                    'shipmentStatus' => $this->shipmentStatusLabel($order),
                    'placedAt' => optional($order->placed_at ?: $order->created_at)->toDateTimeString(),
                    'total' => $order->total?->formatted ?: '-',
                    'subTotal' => $order->sub_total?->formatted ?: '-',
                    'shippingTotal' => $order->shipping_total?->formatted ?: '-',
                    'taxTotal' => $order->tax_total?->formatted ?: '-',
                    'discountTotal' => $order->discount_total?->formatted ?: '-',
                    'items' => $order->product_lines_count,
                    'lines' => $order->productLines->map(fn ($line) => [
                        'id' => $line->id,
                        'name' => $line->description,
                        'option' => $line->option,
                        'image' => $line->purchasable?->getThumbnail()?->getUrl(),
                        'quantity' => $line->quantity,
                        'unitPrice' => $line->unit_price?->formatted ?: '-',
                        'lineTotal' => $line->total?->formatted ?: '-',
                    ])->values()->all(),
                ])
                ->values();

            $ordersPaginator->setCollection($orders);
            $orders = $ordersPaginator->items();
            $pagination = StorefrontData::pagination($ordersPaginator);
        }

        return Inertia::render('Account/Index', [
            'account' => [
                'name' => $user?->name,
                'email' => $user?->email,
            ],
            'orders' => $orders,
            'ordersPagination' => $pagination,
            'history' => $history,
        ]);
    }

    private function shipmentStatusLabel(Order $order): string
    {
        $metaStatus = (string) data_get($order->meta, 'shipment_status', '');
        if ($metaStatus !== '') {
            return Str::of($metaStatus)
                ->replace(['-', '_'], ' ')
                ->title()
                ->toString();
        }

        return match ($this->shipmentBucketFromStatus((string) $order->status)) {
            'delivered' => 'Delivered',
            'shipped' => 'Shipped',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };
    }

    private function shipmentBucketFromStatus(string $status): string
    {
        $value = Str::lower($status);

        if (Str::contains($value, ['deliver'])) {
            return 'delivered';
        }

        if (Str::contains($value, ['ship', 'dispatch', 'fulfil', 'fulfill'])) {
            return 'shipped';
        }

        if (Str::contains($value, ['cancel', 'refund', 'fail'])) {
            return 'cancelled';
        }

        return 'pending';
    }
}
