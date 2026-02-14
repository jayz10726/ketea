<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Consumable;
use App\Models\Issuance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssuanceController extends Controller
{
    public function index(Request $request)
    {
        $issuances = Issuance::with(['issuable', 'recipient', 'issuer'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $users       = User::orderBy('name')->get();
        $assets      = Asset::where('status', 'Available')->get();
        $consumables = Consumable::where('quantity_in_stock', '>', 0)->get();

        return view('issuances.index', compact('issuances', 'users', 'assets', 'consumables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'issuable_type'        => 'required|in:asset,consumable',
            'issuable_id'          => 'required|integer',
            'issued_to'            => 'required|exists:users,id',
            'quantity'             => 'nullable|integer|min:1',
            'issue_date'           => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:issue_date',
            'purpose'              => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $type = $request->issuable_type;
            $id   = $request->issuable_id;
            $qty  = $request->quantity ?? 1;

            if ($type === 'asset') {
                $item = Asset::findOrFail($id);
                abort_if($item->status !== 'Available', 422, 'This asset is not available.');
                $item->update(['status' => 'Issued']);
                $morphClass = Asset::class;
            } else {
                $item = Consumable::findOrFail($id);
                abort_if($item->quantity_in_stock < $qty, 422, 'Insufficient stock.');
                $item->decrement('quantity_in_stock', $qty);
                $morphClass = Consumable::class;
            }

            Issuance::create([
                'issuable_type'        => $morphClass,
                'issuable_id'          => $id,
                'issued_to'            => $request->issued_to,
                'issued_by'            => auth()->id(),
                'quantity'             => $qty,
                'issue_date'           => $request->issue_date ?? now(),
                'expected_return_date' => $request->expected_return_date,
                'purpose'              => $request->purpose,
                'status'               => 'Issued',
            ]);
        });

        return back()->with('success', 'Item issued successfully.');
    }

    public function returnItem(Issuance $issuance)
    {
        abort_if($issuance->status === 'Returned', 422, 'This item was already returned.');

        DB::transaction(function () use ($issuance) {
            $issuance->update([
                'status'             => 'Returned',
                'actual_return_date' => now()->toDateString(),
            ]);

            $item = $issuance->issuable;

            if ($item instanceof Asset) {
                $item->update(['status' => 'Available']);
            } else {
                $item->increment('quantity_in_stock', $issuance->quantity);
            }
        });

        return back()->with('success', 'Item returned successfully.');
    }
}