<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'employees=' . App\Models\Employee::count() . PHP_EOL;
echo 'users=' . App\Models\User::count() . PHP_EOL;
