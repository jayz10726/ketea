<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Consumable;
use App\Models\Issuance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $lowStock        = Consumable::whereColumn('quantity_in_stock', '<=', 'reorder_level')->get();
        $recentIssuances = Issuance::with(['issuable', 'recipient'])->latest()->take(20)->get();
        $assetsByStatus  = Asset::selectRaw('status, count(*) as total')
                               ->groupBy('status')
                               ->pluck('total', 'status');

        return view('reports.index', compact('lowStock', 'recentIssuances', 'assetsByStatus'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->type ?? 'stock';

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            match ($type) {
                'stock'  => $this->exportStock($handle),
                'assets' => $this->exportAssets($handle),
                'low'    => $this->exportLowStock($handle),
                default  => $this->exportStock($handle),
            };

            fclose($handle);
        }, $type . '_report_' . now()->format('Ymd') . '.csv');
    }

    private function exportStock($handle): void
    {
        fputcsv($handle, ['Name', 'Category', 'Stock', 'Reorder Level', 'Unit', 'Status']);
        Consumable::all()->each(fn($c) => fputcsv($handle, [
            $c->name, $c->category, $c->quantity_in_stock,
            $c->reorder_level, $c->unit,
            $c->isLowStock() ? 'LOW STOCK' : 'OK',
        ]));
    }

    private function exportAssets($handle): void
    {
        fputcsv($handle, ['Code', 'Name', 'Category', 'Status', 'Condition', 'Location']);
        Asset::all()->each(fn($a) => fputcsv($handle, [
            $a->asset_code, $a->name, $a->category,
            $a->status, $a->condition, $a->location,
        ]));
    }

    private function exportLowStock($handle): void
    {
        fputcsv($handle, ['Name', 'Category', 'Current Stock', 'Reorder Level', 'Unit']);
        Consumable::whereColumn('quantity_in_stock', '<=', 'reorder_level')
            ->get()->each(fn($c) => fputcsv($handle, [
                $c->name, $c->category,
                $c->quantity_in_stock, $c->reorder_level, $c->unit,
            ]));
    }
}