<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Bouquet;

$b = Bouquet::first();
$items = [];
$gross = 10000;
if ($b) {
    $items = ['bouquet' => $b->only(['id','name','price'])];
    $gross = (int)($b->price ?: 10000);
} else {
    $items = ['custom' => ['note' => 'test order']];
}

$order = Order::create([
    'buyer_name' => 'Test Buyer',
    'buyer_phone' => '081234567890',
    'order_number' => uniqid('ORD'),
    'items' => $items,
    'payment_method' => 'midtrans',
    'status' => 'new',
]);

echo json_encode(['id' => $order->id, 'order_number' => $order->order_number, 'gross' => $gross]) . "\n";
