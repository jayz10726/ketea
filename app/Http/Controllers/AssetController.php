<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        try {
            $data = $request->validate([
                'name'          => 'required|string|max:255',
                'asset_code'    => 'nullable|string|max:50|unique:assets,asset_code',
                'category'      => 'required|string|max:100',
                'serial_number' => 'nullable|string|max:100',
                'condition'     => 'required|in:New,Good,Damaged',
                'location'      => 'nullable|string|max:255',
                'status'        => 'required|in:Available,Issued,Under Maintenance',
                'purchase_date' => 'nullable|date',
                'notes'         => 'nullable|string',
            ]);

            // Check for duplicate serial number if provided
            if (!empty($data['serial_number'])) {
                $exists = Asset::where('serial_number', $data['serial_number'])->exists();
                if ($exists) {
                    throw ValidationException::withMessages([
                        'serial_number' => 'An asset with this serial number already exists.',
                    ]);
                }
            }

            Asset::create(array_merge($data, ['created_by' => auth()->id()]));

            return back()->with('success', 'Asset added successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add asset: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Asset $asset)
    {
        try {
            $data = $request->validate([
                'name'          => 'required|string|max:255',
                'asset_code'    => 'nullable|string|max:50|unique:assets,asset_code,' . $asset->id,
                'category'      => 'required|string|max:100',
                'serial_number' => 'nullable|string|max:100',
                'condition'     => 'required|in:New,Good,Damaged',
                'location'      => 'nullable|string|max:255',
                'status'        => 'required|in:Available,Issued,Under Maintenance',
                'purchase_date' => 'nullable|date',
                'notes'         => 'nullable|string',
            ]);

            // Check duplicate serial number (excluding current asset)
            if (!empty($data['serial_number'])) {
                $exists = Asset::where('serial_number', $data['serial_number'])
                    ->where('id', '!=', $asset->id)
                    ->exists();
                if ($exists) {
                    throw ValidationException::withMessages([
                        'serial_number' => 'An asset with this serial number already exists.',
                    ]);
                }
            }

            $asset->update($data);
            return back()->with('success', 'Asset updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update asset: ' . $e->getMessage());
        }
    }

    public function destroy(Asset $asset)
    {
        try {
            if ($asset->status === 'Issued') {
                return back()->with('error', 'Cannot delete an asset that is currently issued.');
            }

            $asset->delete();
            return back()->with('success', 'Asset deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete asset: ' . $e->getMessage());
        }
    }
}