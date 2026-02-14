<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use Illuminate\Http\Request;

class ConsumableController extends Controller
{
    public function index(Request $request)
    {
        $consumables = Consumable::with('creator')
            ->when($request->search,   fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('consumables.index', compact('consumables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'quantity_in_stock' => 'required|integer|min:0',
            'reorder_level'     => 'required|integer|min:0',
            'unit'              => 'required|string|max:50',
            'notes'             => 'nullable|string',
        ]);

        Consumable::create(array_merge($data, ['created_by' => auth()->id()]));

        return back()->with('success', 'Consumable added.');
    }

    public function update(Request $request, Consumable $consumable)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'category'          => 'required|string|max:100',
            'quantity_in_stock' => 'required|integer|min:0',
            'reorder_level'     => 'required|integer|min:0',
            'unit'              => 'required|string|max:50',
            'notes'             => 'nullable|string',
        ]);

        $consumable->update($data);
        return back()->with('success', 'Consumable updated.');
    }

    public function destroy(Consumable $consumable)
    {
        $consumable->delete();
        return back()->with('success', 'Consumable deleted.');
    }
}