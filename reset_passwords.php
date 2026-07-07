<?php
// reset_passwords.php — Mereset password akun admin dan user menjadi 'password'

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== MEMULAI RESET PASSWORD ===\n";

// 1. Reset Admin
$admin = User::where('email', 'admin')->first();
if ($admin) {
    $admin->password = Hash::make('password');
    $admin->save();
    echo "✔ Sukses reset password untuk username: 'admin' menjadi 'password'\n";
} else {
    echo "✘ User 'admin' tidak ditemukan.\n";
}

// 2. Reset User
$user = User::where('email', 'user')->first();
if ($user) {
    $user->password = Hash::make('password');
    $user->save();
    echo "✔ Sukses reset password untuk username: 'user' menjadi 'password'\n";
} else {
    echo "✘ User 'user' tidak ditemukan.\n";
}

echo "=== RESET SELESAI ===\n";
