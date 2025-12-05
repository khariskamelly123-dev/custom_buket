<?php
// Boot Laravel application and create seller user
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::firstOrCreate([
    'email' => 'seller@example.com',
], [
    'name' => 'Seller User',
    'password' => 'password',
    'role' => 'seller',
]);

if ($user->wasRecentlyCreated) {
    echo "Seller user created: seller@example.com (password: password)\n";
} else {
    echo "Seller user already exists: seller@example.com\n";
}
