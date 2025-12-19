<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$payload = [
    'order_id' => 'ORD693ba3790d056',
    'status_code' => '200',
    'gross_amount' => '120000',
    'transaction_id' => 'trx-test-0001',
    'transaction_status' => 'settlement',
    'signature_key' => 'invalidsig'
];

$serverKey = env('MIDTRANS_SERVER_KEY');
$orderId = $payload['order_id'] ?? null;
$statusCode = isset($payload['status_code']) ? (string)$payload['status_code'] : '';
$gross = isset($payload['gross_amount']) ? (string)$payload['gross_amount'] : '';
$signature = $payload['signature_key'] ?? '';

if ($serverKey && $orderId !== null) {
    $expected = hash('sha512', $orderId . $statusCode . $gross . $serverKey);
    if ($signature !== $expected) {
        echo "Invalid signature\n";
        exit(1);
    }
}

$order = Order::where('order_number', $orderId)->first();
if (!$order) {
    echo "Order not found\n";
    exit(2);
}

$transactionId = $payload['transaction_id'] ?? null;
$transactionStatus = $payload['transaction_status'] ?? ($payload['status_message'] ?? null);

if ($transactionId) $order->payment_transaction_id = $transactionId;
if ($transactionStatus) $order->payment_status = $transactionStatus;

if (in_array($transactionStatus, ['settlement', 'capture', 'paid'])) {
    $order->status = 'paid';
} elseif (in_array($transactionStatus, ['pending'])) {
    $order->status = 'pending_payment';
} elseif (in_array($transactionStatus, ['deny', 'cancel', 'expired', 'failure'])) {
    $order->status = 'payment_failed';
}

$order->save();

echo "OK - order {$order->order_number} updated to status {$order->status}\n";
