<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Delivery;
use App\Models\DeliveryDriver;
use App\Models\Food;
use App\Models\FoodAddon;
use App\Models\FoodAddonItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    /**
     * Seed a realistic set of orders, deliveries, items and payments so the
     * dashboard, orders and delivery UI have live data to display.
     */
    public function run(): void
    {
        if (Order::count() > 0) {
            $this->command->warn('Orders already exist — skipping OrderSeeder.');
            return;
        }

        $branchId = 1;
        $tableId = 1;
        $promotion = Promotion::where('code', 'SUMMER20')->first();
        $foods = Food::pluck('price', 'id')->toArray();

        $customers = $this->createCustomers();
        $drivers = $this->createDrivers();

        $extras = $this->createAddons();
        $addonPrices = [
            $extras['prawns'] => 2.50,
            $extras['coconutRice'] => 1.50,
            $extras['fries'] => 1.00,
            $extras['salad'] => 1.25,
        ];

        $orderSpecs = [
            [
                'number' => 'ORD-1001', 'type' => 'delivery', 'status' => 'completed', 'price' => true,
                'days' => 7, 'time' => '18:47', 'customer' => 'jane',
                'promo' => true,
                'items' => [
                    ['food' => 4, 'qty' => 2, 'addon' => $extras['prawns']],
                    ['food' => 10, 'qty' => 1, 'addon' => $extras['fries']],
                ],
                'delivery' => ['status' => 'delivered', 'driver' => 'rithy', 'notes' => 'Leave at the front desk.'],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => 'Extra chili sauce on the side please.',
            ],
            [
                'number' => 'ORD-1002', 'type' => 'dine_in', 'status' => 'completed', 'price' => true,
                'days' => 6, 'time' => '12:20', 'customer' => 'dara',
                'promo' => false,
                'items' => [
                    ['food' => 7, 'qty' => 2],
                    ['food' => 23, 'qty' => 1],
                    ['food' => 21, 'qty' => 1],
                ],
                'payment' => ['method' => 'cash'],
                'notes' => 'Table birthday — brought a small cake.',
            ],
            [
                'number' => 'ORD-1003', 'type' => 'takeaway', 'status' => 'completed', 'price' => true,
                'days' => 5, 'time' => '08:12', 'customer' => 'kunthea',
                'promo' => true,
                'items' => [
                    ['food' => 22, 'qty' => 2],
                    ['food' => 18, 'qty' => 1],
                ],
                'payment' => ['method' => 'aba_card'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1004', 'type' => 'delivery', 'status' => 'completed', 'price' => true,
                'days' => 5, 'time' => '19:05', 'customer' => 'meas',
                'promo' => false,
                'items' => [
                    ['food' => 11, 'qty' => 2],
                    ['food' => 9, 'qty' => 1],
                ],
                'delivery' => ['status' => 'delivered', 'driver' => 'sokha', 'notes' => 'Call when arriving at the gate.'],
                'payment' => ['method' => 'cash'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1005', 'type' => 'delivery', 'status' => 'cancelled', 'price' => false,
                'days' => 4, 'time' => '20:30', 'customer' => 'rith',
                'promo' => false,
                'items' => [
                    ['food' => 13, 'qty' => 2],
                    ['food' => 17, 'qty' => 1],
                ],
                'payment' => null,
                'notes' => 'Customer cancelled before prep — refund issued.',
            ],
            [
                'number' => 'ORD-1006', 'type' => 'dine_in', 'status' => 'completed', 'price' => true,
                'days' => 4, 'time' => '13:00', 'customer' => 'piseth',
                'promo' => false,
                'items' => [
                    ['food' => 5, 'qty' => 1],
                    ['food' => 4, 'qty' => 1],
                    ['food' => 18, 'qty' => 1],
                ],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1007', 'type' => 'takeaway', 'status' => 'completed', 'price' => true,
                'days' => 3, 'time' => '11:45', 'customer' => 'dara',
                'promo' => false,
                'items' => [
                    ['food' => 20, 'qty' => 3],
                ],
                'payment' => ['method' => 'cash'],
                'notes' => 'Wrap each bowl separately.',
            ],
            [
                'number' => 'ORD-1008', 'type' => 'delivery', 'status' => 'completed', 'price' => true,
                'days' => 3, 'time' => '19:22', 'customer' => 'kunthea',
                'promo' => false,
                'items' => [
                    ['food' => 6, 'qty' => 2],
                    ['food' => 12, 'qty' => 1],
                ],
                'delivery' => ['status' => 'delivered', 'driver' => 'rithy', 'notes' => null],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1009', 'type' => 'delivery', 'status' => 'completed', 'price' => true,
                'days' => 2, 'time' => '12:10', 'customer' => 'jane',
                'promo' => false,
                'items' => [
                    ['food' => 19, 'qty' => 2],
                    ['food' => 16, 'qty' => 1],
                ],
                'delivery' => ['status' => 'delivered', 'driver' => 'sokha', 'notes' => 'Ring the bell twice.'],
                'payment' => ['method' => 'cash'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1010', 'type' => 'dine_in', 'status' => 'completed', 'price' => true,
                'days' => 2, 'time' => '19:40', 'customer' => 'rith',
                'promo' => true,
                'items' => [
                    ['food' => 10, 'qty' => 2, 'addon' => $extras['salad']],
                    ['food' => 7, 'qty' => 1],
                    ['food' => 21, 'qty' => 1],
                ],
                'payment' => ['method' => 'aba_card'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1011', 'type' => 'takeaway', 'status' => 'cancelled', 'price' => false,
                'days' => 1, 'time' => '09:50', 'customer' => 'meas',
                'promo' => false,
                'items' => [
                    ['food' => 9, 'qty' => 1],
                    ['food' => 23, 'qty' => 1],
                ],
                'payment' => null,
                'notes' => 'Order voided — customer did not show.',
            ],
            [
                'number' => 'ORD-1012', 'type' => 'delivery', 'status' => 'completed', 'price' => true,
                'days' => 1, 'time' => '20:05', 'customer' => 'piseth',
                'promo' => false,
                'items' => [
                    ['food' => 14, 'qty' => 1],
                    ['food' => 11, 'qty' => 2],
                    ['food' => 17, 'qty' => 1],
                ],
                'delivery' => ['status' => 'delivered', 'driver' => 'sokha', 'notes' => null],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1013', 'type' => 'delivery', 'status' => 'out_for_delivery', 'price' => false,
                'days' => 0, 'time' => '10:30', 'customer' => 'kunthea',
                'promo' => false,
                'items' => [
                    ['food' => 4, 'qty' => 1],
                    ['food' => 10, 'qty' => 2],
                ],
                'delivery' => ['status' => 'out_for_delivery', 'driver' => 'rithy', 'notes' => null],
                'payment' => ['method' => 'cash'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1014', 'type' => 'dine_in', 'status' => 'preparing', 'price' => false,
                'days' => 0, 'time' => '11:15', 'customer' => 'dara',
                'promo' => false,
                'items' => [
                    ['food' => 7, 'qty' => 2],
                    ['food' => 18, 'qty' => 1],
                ],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1015', 'type' => 'takeaway', 'status' => 'ready', 'price' => true,
                'days' => 0, 'time' => '11:40', 'customer' => 'meas',
                'promo' => false,
                'items' => [
                    ['food' => 20, 'qty' => 2],
                    ['food' => 21, 'qty' => 1],
                ],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => 'Bag with handles please.',
            ],
            [
                'number' => 'ORD-1016', 'type' => 'delivery', 'status' => 'pending', 'price' => false,
                'days' => 0, 'time' => '11:50', 'customer' => 'jane',
                'promo' => false,
                'items' => [
                    ['food' => 6, 'qty' => 1],
                    ['food' => 16, 'qty' => 1],
                ],
                'delivery' => ['status' => 'pending', 'driver' => null, 'notes' => null],
                'payment' => ['method' => 'cash'],
                'notes' => null,
            ],
            [
                'number' => 'ORD-1017', 'type' => 'dine_in', 'status' => 'confirmed', 'price' => false,
                'days' => 0, 'time' => '12:05', 'customer' => 'rith',
                'promo' => false,
                'items' => [
                    ['food' => 19, 'qty' => 2],
                    ['food' => 23, 'qty' => 1],
                ],
                'payment' => ['method' => 'aba_card'],
                'notes' => 'Guest has a nut allergy.',
            ],
            [
                'number' => 'ORD-1018', 'type' => 'delivery', 'status' => 'out_for_delivery', 'price' => false,
                'days' => 0, 'time' => '12:20', 'customer' => 'piseth',
                'promo' => true,
                'items' => [
                    ['food' => 12, 'qty' => 1],
                    ['food' => 9, 'qty' => 2],
                ],
                'delivery' => ['status' => 'picked_up', 'driver' => 'sokha', 'notes' => 'Perishable — deliver fast.'],
                'payment' => ['method' => 'aba_khqr'],
                'notes' => null,
            ],
        ];

        foreach ($orderSpecs as $spec) {
            $baseTime = Carbon::now()->subDays($spec['days'])->setTimeFromTimeString($spec['time']);

            $items = $this->buildItems($spec['items'], $foods, $addonPrices);
            $subtotal = $this->sumItems($items);

            $discount = $spec['promo'] && $promotion ? round($subtotal * 0.2, 2) : 0;
            $tax = round($subtotal * 0.1, 2);
            $serviceCharge = $spec['type'] === 'dine_in' ? round($subtotal * 0.05, 2) : 0;
            $deliveryFee = $spec['type'] === 'delivery' ? (($subtotal - $discount) >= 15 ? 0 : 2) : 0;
            $grandTotal = round($subtotal - $discount + $tax + $serviceCharge + $deliveryFee, 2);

            $order = Order::create([
                'branch_id' => $branchId,
                'table_id' => $spec['type'] === 'dine_in' ? $tableId : null,
                'customer_id' => $customers[$spec['customer']]->id,
                'promotion_id' => $spec['promo'] && $promotion ? $promotion->id : null,
                'order_number' => $spec['number'],
                'order_type' => $spec['type'],
                'status' => $spec['status'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'service_charge' => $serviceCharge,
                'delivery_fee' => $deliveryFee,
                'grand_total' => $grandTotal,
                'notes' => $spec['notes'],
                'created_at' => $baseTime,
                'updated_at' => $baseTime,
            ]);

            $orderItems = [];
            foreach ($items as $item) {
                $orderItems[] = $this->createOrderItem($order, $foods, $item);
            }

            $this->createStatusHistory($order, $spec['status'], $baseTime);

            if ($spec['type'] === 'delivery' && $spec['status'] !== 'cancelled' && ! empty($spec['delivery'])) {
                $this->createDelivery($order, $customers[$spec['customer']], $spec['delivery'], $deliveryFee, $drivers, $baseTime);
            }

            if (! empty($spec['payment'])) {
                $this->createPayment($order, $spec['payment'], $grandTotal, $spec['price']);
            }
        }
    }

    private function createCustomers(): array
    {
        $defs = [
            'jane' => ['name' => 'Jane Smith', 'phone' => '0987654321', 'email' => 'jane@example.com'],
            'dara' => ['name' => 'Chen Dara', 'phone' => '012345678', 'email' => 'dara@example.com'],
            'kunthea' => ['name' => 'Sok Kunthea', 'phone' => '012345679', 'email' => 'kunthea@example.com'],
            'rith' => ['name' => 'Bun Rith', 'phone' => '012345680', 'email' => 'rith@example.com'],
            'meas' => ['name' => 'Apsara Meas', 'phone' => '012345681', 'email' => 'meas@example.com'],
            'piseth' => ['name' => 'Tek Piseth', 'phone' => '012345682', 'email' => 'piseth@example.com'],
        ];

        $addressDefs = [
            'dara' => [['label' => 'Home', 'address' => '#22 Street 240, Sangkat Chaktomuk', 'city' => 'Phnom Penh'], ['label' => 'Work', 'address' => 'Tower 4, #14 Street 200', 'city' => 'Phnom Penh']],
            'kunthea' => [['label' => 'Home', 'address' => '#126 Street 360, Boeung Keng Kang', 'city' => 'Phnom Penh']],
            'rith' => [['label' => 'Home', 'address' => '#8 Street 51, Boeung Keng Kang', 'city' => 'Phnom Penh']],
            'meas' => [['label' => 'Home', 'address' => '#45 Street 63, Tonle Bassac', 'city' => 'Phnom Penh']],
            'piseth' => [['label' => 'Home', 'address' => '#90 Street 110, Tuol Kork', 'city' => 'Phnom Penh']],
        ];

        $result = [];
        foreach ($defs as $key => $def) {
            $customer = Customer::updateOrCreate(['email' => $def['email']], $def);

            $addresses = $addressDefs[$key] ?? [];
            foreach ($addresses as $i => $addr) {
                CustomerAddress::updateOrCreate([
                    'customer_id' => $customer->id,
                    'label' => $addr['label'],
                ], [
                    'address' => $addr['address'],
                    'city' => $addr['city'],
                    'phone' => $customer->phone,
                    'is_default' => $i === 0 ? 1 : 0,
                ]);
            }

            $result[$key] = $customer;
        }

        return $result;
    }

    private function createDrivers(): array
    {
        $defs = [
            'rithy' => ['name' => 'Dara Chea', 'email' => 'dara.chea@example.com', 'phone' => '010111222', 'vehicle' => 'Motorbike', 'plate' => 'PP 1K 8821'],
            'sokha' => ['name' => 'Sokha Lim', 'email' => 'sokha.lim@example.com', 'phone' => '010333444', 'vehicle' => 'Car', 'plate' => 'PP 2B 3312'],
        ];

        $result = [
            'jane' => DeliveryDriver::first(), // pre-existing driver from earlier seeds
        ];

        foreach ($defs as $key => $def) {
            $user = User::firstOrCreate(
                ['email' => $def['email']],
                [
                    'name' => $def['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $def['phone'],
                ],
            );

            $driver = DeliveryDriver::firstOrCreate(
                ['phone' => $def['phone']],
                [
                    'user_id' => $user->id,
                    'branch_id' => 1,
                    'vehicle_type' => $def['vehicle'],
                    'license_plate' => $def['plate'],
                    'status' => 'available',
                ],
            );

            $result[$key] = $driver;
        }

        return $result;
    }

    private function createAddons(): array
    {
        $amokExtras = FoodAddon::updateOrCreate(
            ['food_id' => 4, 'name' => 'Extras'],
            ['is_required' => false, 'max_items' => 3, 'is_available' => true],
        );
        $prawns = FoodAddonItem::updateOrCreate(
            ['addon_id' => $amokExtras->id, 'name' => 'Grilled Prawns'],
            ['price' => 2.50, 'is_available' => true],
        );
        $coconutRice = FoodAddonItem::updateOrCreate(
            ['addon_id' => $amokExtras->id, 'name' => 'Coconut Rice'],
            ['price' => 1.50, 'is_available' => true],
        );

        $chickenSides = FoodAddon::updateOrCreate(
            ['food_id' => 10, 'name' => 'Sides'],
            ['is_required' => false, 'max_items' => 2, 'is_available' => true],
        );
        $fries = FoodAddonItem::updateOrCreate(
            ['addon_id' => $chickenSides->id, 'name' => 'French Fries'],
            ['price' => 1.00, 'is_available' => true],
        );
        $salad = FoodAddonItem::updateOrCreate(
            ['addon_id' => $chickenSides->id, 'name' => 'Garden Salad'],
            ['price' => 1.25, 'is_available' => true],
        );

        return [
            'prawns' => $prawns->id,
            'coconutRice' => $coconutRice->id,
            'fries' => $fries->id,
            'salad' => $salad->id,
        ];
    }

    private function buildItems(array $specs, array $foods, array $addonPrices): array
    {
        $items = [];
        foreach ($specs as $spec) {
            $unit = round((float) ($foods[$spec['food']] ?? 0), 2);
            if (! empty($spec['addon'])) {
                $unit += round((float) ($addonPrices[$spec['addon']] ?? 0), 2);
            }

            $items[] = [
                'food_id' => $spec['food'],
                'qty' => $spec['qty'],
                'unit_price' => round($unit, 2),
                'addon_id' => $spec['addon'] ?? null,
                'addon_price' => ! empty($spec['addon']) ? ($addonPrices[$spec['addon']] ?? 0) : null,
            ];
        }

        return $items;
    }

    private function sumItems(array $items): float
    {
        return round(array_sum(array_map(fn ($i) => $i['unit_price'] * $i['qty'], $items)), 2);
    }

    private function createOrderItem(Order $order, array $foods, array $item): OrderItem
    {
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $item['food_id'],
            'variant_id' => null,
            'quantity' => $item['qty'],
            'unit_price' => $item['unit_price'],
            'subtotal' => round($item['unit_price'] * $item['qty'], 2),
            'notes' => null,
        ]);

        if (! empty($item['addon_id'])) {
            OrderItemAddon::create([
                'order_item_id' => $orderItem->id,
                'addon_item_id' => $item['addon_id'],
                'price' => $item['addon_price'],
            ]);
        }

        return $orderItem;
    }

    private function createStatusHistory(Order $order, string $finalStatus, Carbon $baseTime): void
    {
        $sequences = [
            'pending' => ['pending'],
            'confirmed' => ['pending', 'confirmed'],
            'preparing' => ['pending', 'confirmed', 'preparing'],
            'ready' => ['pending', 'confirmed', 'preparing', 'ready'],
            'out_for_delivery' => ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'],
            'completed' => ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'completed'],
            'cancelled' => ['pending', 'confirmed', 'cancelled'],
        ];

        $steps = $sequences[$finalStatus] ?? ['pending'];
        $offset = 0;
        foreach ($steps as $status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $status,
                'changed_by' => 'Restaurant Admin',
                'notes' => $status === 'cancelled' ? $order->notes : null,
                'created_at' => (clone $baseTime)->addMinutes($offset),
                'updated_at' => (clone $baseTime)->addMinutes($offset),
            ]);
            $offset += 6;
        }
    }

    private function createDelivery(Order $order, Customer $customer, array $spec, float $deliveryFee, array $drivers, Carbon $baseTime): void
    {
        $address = CustomerAddress::where('customer_id', $customer->id)->where('is_default', true)->first();

        Delivery::create([
            'order_id' => $order->id,
            'delivery_driver_id' => $spec['driver'] ? $drivers[$spec['driver']]->id : null,
            'customer_address_id' => $address?->id,
            'status' => $spec['status'],
            'delivery_fee' => $deliveryFee,
            'notes' => $spec['notes'],
            'created_at' => (clone $baseTime)->addMinutes(3),
            'updated_at' => (clone $baseTime)->addMinutes(20),
        ]);
    }

    private function createPayment(Order $order, array $spec, float $amount, bool $paid): void
    {
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => $spec['method'],
            'amount' => $amount,
            'status' => $paid ? 'paid' : 'unpaid',
            'transaction_reference' => $paid ? 'TXN-' . $order->order_number : null,
            'created_at' => $order->created_at->addMinutes(2),
            'updated_at' => $order->created_at->addMinutes(2),
        ]);
    }
}