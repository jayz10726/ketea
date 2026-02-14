<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::with('creator')
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('asset_code', 'like', "%$s%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->status,   fn($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('assets.index', compact('assets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'asset_code'    => 'nullable|unique:assets,asset_code',
            'category'      => 'required|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'condition'     => 'required|in:New,Good,Damaged',
            'location'      => 'nullable|string|max:255',
            'status'        => 'required|in:Available,Issued,Under Maintenance',
            'purchase_date' => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        Asset::create(array_merge($data, ['created_by' => auth()->id()]));

        return back()->with('success', 'Asset added successfully.');
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'asset_code'    => 'nullable|unique:assets,asset_code,' . $asset->id,
            'category'      => 'required|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'condition'     => 'required|in:New,Good,Damaged',
            'location'      => 'nullable|string|max:255',
            'status'        => 'required|in:Available,Issued,Under Maintenance',
            'purchase_date' => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        $asset->update($data);

        return back()->with('success', 'Asset updated.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->status === 'Issued') {
            return back()->with('error', 'Cannot delete an asset that is currently issued.');
        }

        $asset->delete();
        return back()->with('success', 'Asset deleted.');
    }
}