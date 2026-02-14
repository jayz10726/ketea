<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Consumable;
use App\Models\Issuance;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_assets'      => Asset::count(),
            'total_consumables' => Consumable::count(),
            'issued_assets'     => Asset::where('status', 'Issued')->count(),
            'low_stock_count'   => Consumable::whereColumn('quantity_in_stock', '<=', 'reorder_level')->count(),
            'total_staff'       => User::where('role', 'staff')->count(),
            'overdue'           => Issuance::where('status', 'Issued')
                                      ->where('expected_return_date', '<', now())
                                      ->count(),
        ];

        $lowStockItems = Consumable::whereColumn('quantity_in_stock', '<=', 'reorder_level')
                            ->take(5)->get();

        $recentIssuances = Issuance::with(['issuable', 'recipient'])
                               ->latest()->take(8)->get();

        $assetChartData = Asset::selectRaw('status, count(*) as total')
                              ->groupBy('status')
                              ->pluck('total', 'status');

        $consumableChartData = Consumable::selectRaw('category, sum(quantity_in_stock) as total')
                                   ->groupBy('category')
                                   ->pluck('total', 'category');

        return view('dashboard', compact(
            'stats', 'lowStockItems', 'recentIssuances',
            'assetChartData', 'consumableChartData'
        ));
    }
}