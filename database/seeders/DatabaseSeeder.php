<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Consumable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        User::create(['name' => 'Admin User',   'email' => 'admin@ketea.com',
            'password' => Hash::make('password'), 'role' => 'admin',       'department' => 'IT']);
        User::create(['name' => 'Store Keeper', 'email' => 'store@ketea.com',
            'password' => Hash::make('password'), 'role' => 'storekeeper', 'department' => 'Stores']);
        User::create(['name' => 'John Staff',   'email' => 'staff@ketea.com',
            'password' => Hash::make('password'), 'role' => 'staff',       'department' => 'Finance']);

        // Create assets
        foreach ([
            ['name' => 'Dell Laptop',        'category' => 'Electronics', 'condition' => 'New',  'location' => 'IT Room',   'status' => 'Available'],
            ['name' => 'Office Chair',        'category' => 'Furniture',   'condition' => 'Good', 'location' => 'Block A',   'status' => 'Available'],
            ['name' => 'HP Printer',          'category' => 'Electronics', 'condition' => 'Good', 'location' => 'Admin',     'status' => 'Available'],
            ['name' => 'Projector',           'category' => 'Electronics', 'condition' => 'Good', 'location' => 'Boardroom', 'status' => 'Available'],
            ['name' => 'Fire Extinguisher',   'category' => 'Equipment',   'condition' => 'New',  'location' => 'Corridor',  'status' => 'Available'],
        ] as $a) {
            Asset::create(array_merge($a, ['created_by' => 1, 'purchase_date' => now()->subMonths(rand(1, 24))]));
        }

        // Create consumables (2 are intentionally low stock to test alerts)
        foreach ([
            ['name' => 'A4 Paper',        'category' => 'Stationery',  'quantity_in_stock' => 50, 'reorder_level' => 10, 'unit' => 'reams'],
            ['name' => 'Ballpoint Pens',  'category' => 'Stationery',  'quantity_in_stock' => 3,  'reorder_level' => 10, 'unit' => 'boxes'],  // LOW
            ['name' => 'Toner Cartridge', 'category' => 'Consumables', 'quantity_in_stock' => 5,  'reorder_level' => 2,  'unit' => 'pcs'],
            ['name' => 'Staples',         'category' => 'Stationery',  'quantity_in_stock' => 8,  'reorder_level' => 5,  'unit' => 'boxes'],
            ['name' => 'Marker Pens',     'category' => 'Stationery',  'quantity_in_stock' => 2,  'reorder_level' => 5,  'unit' => 'packs'],  // LOW
        ] as $c) {
            Consumable::create(array_merge($c, ['created_by' => 1]));
        }
    }
}