<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConsumableController extends Controller
{
    public function index(Request $request)
    {
        $consumables = Consumable::with('creator')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('consumables.index', compact('consumables'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name'              => 'required|string|max:255',
                'category'          => 'required|string|max:100',
                'quantity_in_stock' => 'required|integer|min:0',
                'reorder_level'     => 'required|integer|min:0',
                'unit'              => 'required|string|max:50',
                'notes'             => 'nullable|string',
            ]);

            // Check for duplicate name in same category
            $exists = Consumable::where('name', $data['name'])
                ->where('category', $data['category'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'name' => 'A consumable with this name already exists in the ' . $data['category'] . ' category.',
                ]);
            }

            Consumable::create(array_merge($data, ['created_by' => auth()->id()]));

            return back()->with('success', 'Consumable added successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add consumable: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Consumable $consumable)
    {
        try {
            $data = $request->validate([
                'name'              => 'required|string|max:255',
                'category'          => 'required|string|max:100',
                'quantity_in_stock' => 'required|integer|min:0',
                'reorder_level'     => 'required|integer|min:0',
                'unit'              => 'required|string|max:50',
                'notes'             => 'nullable|string',
            ]);

            // Check for duplicate (excluding current item)
            $exists = Consumable::where('name', $data['name'])
                ->where('category', $data['category'])
                ->where('id', '!=', $consumable->id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'name' => 'A consumable with this name already exists in the ' . $data['category'] . ' category.',
                ]);
            }

            $consumable->update($data);
            return back()->with('success', 'Consumable updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update consumable: ' . $e->getMessage());
        }
    }

    public function destroy(Consumable $consumable)
    {
        try {
            $consumable->delete();
            return back()->with('success', 'Consumable deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete consumable: ' . $e->getMessage());
        }
    }
}