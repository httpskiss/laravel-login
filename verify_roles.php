<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Role;

$roles = Role::all();
echo "All Roles:\n";
echo "===========\n";
foreach($roles as $role) {
    echo "- " . $role->name . "\n";
}
