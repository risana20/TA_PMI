<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WargaBinaan;

echo "=== DATA USERS ===\n";
$users = User::all();
foreach ($users as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
}

echo "\n=== STATISTIK ===\n";
echo "Total Users: " . User::count() . "\n";
echo "Total Warga Binaan: " . WargaBinaan::count() . "\n";

