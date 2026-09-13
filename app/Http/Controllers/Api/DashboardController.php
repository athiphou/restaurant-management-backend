<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Food;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\RestaurantBranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $query = $this->baseOrderQuery($request);

        $orders = (clone $query)->get();

        $revenue = (clone $query)->sum('grand_total');
        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $cancelledOrders = $orders->where('status', 'cancelled')->count();
        $totalCustomers = Customer::count();
        $totalFoods = Food::count();
        $activeBranches = RestaurantBranch::count();

        $avgOrderValue = $totalOrders > 0 ? round($revenue / $totalOrders, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'revenue' => round($revenue, 2),
                'orders' => $totalOrders,
                'customers' => $totalCustomers,
                'foods' => $totalFoods,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'active_branches' => $activeBranches,
                'avg_order_value' => $avgOrderValue,
            ],
        ]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $startDate = $this->getStartDate($request);
        $endDate = $this->getEndDate($request);
        $groupBy = $request->get('group_by', 'day');

        $data = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("SUM(grand_total) as revenue"),
                DB::raw("COUNT(*) as order_count")
            )
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get()
            ->map(function ($row) use ($groupBy) {
                $date = Carbon::parse($row->date);
                return [
                    'label' => $date->format('M j'),
                    'value' => round($row->revenue, 2),
                    'orders' => $row->order_count,
                    'date' => $row->date,
                ];
            });

        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

        $prevStartDate = Carbon::parse($startDate)->sub(Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)));
        $prevEndDate = Carbon::parse($startDate)->subDay();

        $prevRevenue = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('grand_total');

        $growth = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100 : 0);

        return response()->json([
            'success' => true,
            'data' => [
                'chart' => $data,
                'total' => round($totalRevenue, 2),
                'growth' => $growth,
            ],
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $startDate = $this->getStartDate($request);
        $endDate = $this->getEndDate($request);

        $statusCounts = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $typeCounts = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('order_type', DB::raw('COUNT(*) as count'))
            ->groupBy('order_type')
            ->get()
            ->pluck('count', 'order_type')
            ->toArray();

        $chart = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as count")
            )
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                $date = Carbon::parse($row->date);
                return [
                    'label' => $date->format('M j'),
                    'value' => $row->count,
                    'date' => $row->date,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'status_counts' => [
                    'pending' => $statusCounts['pending'] ?? 0,
                    'confirmed' => $statusCounts['confirmed'] ?? 0,
                    'preparing' => $statusCounts['preparing'] ?? 0,
                    'ready' => $statusCounts['ready'] ?? 0,
                    'out_for_delivery' => $statusCounts['out_for_delivery'] ?? 0,
                    'completed' => $statusCounts['completed'] ?? 0,
                    'cancelled' => $statusCounts['cancelled'] ?? 0,
                ],
                'type_counts' => [
                    'dine_in' => $typeCounts['dine_in'] ?? 0,
                    'takeaway' => $typeCounts['takeaway'] ?? 0,
                    'delivery' => $typeCounts['delivery'] ?? 0,
                ],
                'chart' => $chart,
            ],
        ]);
    }

    public function topFoods(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);

        $topFoods = OrderItem::select(
                'food_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count')
            )
            ->groupBy('food_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $food = Food::with('category')->find($item->food_id);
                return [
                    'id' => $item->food_id,
                    'name' => $food?->name ?? 'Unknown',
                    'category' => $food?->category?->name ?? '-',
                    'total_quantity' => $item->total_quantity,
                    'total_revenue' => round($item->total_revenue, 2),
                    'order_count' => $item->order_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $topFoods,
        ]);
    }

    public function recentOrders(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);

        $orders = Order::with(['customer', 'branch', 'items.food'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                $items = $order->items->map(function ($item) {
                    return $item->food?->name . ' x' . $item->quantity;
                })->implode(', ');

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->customer?->name ?? 'Guest',
                    'items' => $items,
                    'item_count' => $order->items->sum('quantity'),
                    'grand_total' => round($order->grand_total, 2),
                    'status' => $order->status,
                    'order_type' => $order->order_type,
                    'payment_status' => $order->payments->first()?->status ?? 'pending',
                    'branch' => $order->branch?->name ?? '-',
                    'created_at' => $order->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function recentCustomers(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);

        $customers = Customer::withCount('orders')
            ->withSum('orders', 'grand_total')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'orders_count' => $customer->orders_count ?? 0,
                    'total_spent' => round($customer->orders_sum_grand_total ?? 0, 2),
                    'created_at' => $customer->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function inventory(Request $request): JsonResponse
    {
        $lowStock = Inventory::with(['ingredient', 'branch'])
            ->whereColumn('quantity', '<=', DB::raw('(SELECT reorder_level FROM ingredients WHERE ingredients.id = inventory.ingredient_id)'))
            ->where('quantity', '>', 0)
            ->orderBy('quantity')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'ingredient' => $inv->ingredient?->name ?? '-',
                    'branch' => $inv->branch?->name ?? '-',
                    'quantity' => $inv->quantity,
                    'unit' => $inv->ingredient?->unit ?? 'unit',
                    'reorder_level' => $inv->ingredient?->reorder_level ?? 0,
                ];
            });

        $outOfStock = Inventory::with(['ingredient', 'branch'])
            ->where('quantity', '<=', 0)
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'ingredient' => $inv->ingredient?->name ?? '-',
                    'branch' => $inv->branch?->name ?? '-',
                    'quantity' => $inv->quantity,
                    'unit' => $inv->ingredient?->unit ?? 'unit',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
                'low_stock_count' => $lowStock->count(),
                'out_of_stock_count' => $outOfStock->count(),
            ],
        ]);
    }

    public function payments(Request $request): JsonResponse
    {
        $startDate = $this->getStartDate($request);
        $endDate = $this->getEndDate($request);

        $paymentStats = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get();

        $methodStats = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'by_status' => [
                    'paid' => [
                        'count' => $paymentStats->where('status', 'paid')->first()?->count ?? 0,
                        'total' => round($paymentStats->where('status', 'paid')->first()?->total ?? 0, 2),
                    ],
                    'pending' => [
                        'count' => $paymentStats->where('status', 'pending')->first()?->count ?? 0,
                        'total' => round($paymentStats->where('status', 'pending')->first()?->total ?? 0, 2),
                    ],
                    'failed' => [
                        'count' => $paymentStats->where('status', 'failed')->first()?->count ?? 0,
                        'total' => round($paymentStats->where('status', 'failed')->first()?->total ?? 0, 2),
                    ],
                    'refunded' => [
                        'count' => $paymentStats->where('status', 'refunded')->first()?->count ?? 0,
                        'total' => round($paymentStats->where('status', 'refunded')->first()?->total ?? 0, 2),
                    ],
                ],
                'by_method' => $methodStats->map(fn($m) => [
                    'method' => $m->payment_method,
                    'count' => $m->count,
                    'total' => round($m->total, 2),
                ]),
            ],
        ]);
    }

    private function baseOrderQuery(Request $request)
    {
        $query = Order::query();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $startDate = $this->getStartDate($request);
        $endDate = $this->getEndDate($request);
        $query->whereBetween('created_at', [$startDate, $endDate]);

        return $query;
    }

    private function getStartDate(Request $request): Carbon
    {
        $period = $request->get('period', 'all');
        $from = $request->get('from');
        $to = $request->get('to');

        if ($from && $to) {
            return Carbon::parse($from)->startOfDay();
        }

        return match ($period) {
            'today' => Carbon::today(),
            'yesterday' => Carbon::yesterday(),
            'last_7_days' => Carbon::now()->subDays(6)->startOfDay(),
            'last_30_days' => Carbon::now()->subDays(29)->startOfDay(),
            'this_month' => Carbon::now()->startOfMonth(),
            'last_month' => Carbon::now()->subMonth()->startOfMonth(),
            'this_year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subYear()->startOfDay(),
        };
    }

    private function getEndDate(Request $request): Carbon
    {
        $to = $request->get('to');
        if ($to) {
            return Carbon::parse($to)->endOfDay();
        }
        return Carbon::now()->endOfDay();
    }
}
