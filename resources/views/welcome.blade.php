@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- ── Stat Cards ───────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    @php
    $cards = [
        ['label' => 'Total Assets',      'value' => $stats['total_assets'],      'color' => 'blue',   'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Consumables',        'value' => $stats['total_consumables'], 'color' => 'emerald','icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4'],
        ['label' => 'Issued Assets',      'value' => $stats['issued_assets'],     'color' => 'amber',  'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        ['label' => 'Low Stock',          'value' => $stats['low_stock_count'],   'color' => 'red',    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label' => 'Staff Members',      'value' => $stats['total_staff'],       'color' => 'violet', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['label' => 'Overdue Returns',    'value' => $stats['overdue'],           'color' => 'rose',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    @endphp

    @foreach($cards as $card)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</p>
    </div>
    @endforeach

</div>

{{-- ── Charts ───────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="card">
        <h3 class="font-semibold text-gray-700 mb-4">Asset Status Breakdown</h3>
        <canvas id="assetChart" height="200"></canvas>
    </div>
    <div class="card">
        <h3 class="font-semibold text-gray-700 mb-4">Consumable Stock by Category</h3>
        <canvas id="consumableChart" height="200"></canvas>
    </div>
</div>

{{-- ── Bottom Row ───────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Low Stock Alerts --}}
    <div class="card">
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            Low Stock Alerts
        </h3>
        @forelse($lowStockItems as $item)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $item->name }}</p>
                    <p class="text-xs text-gray-500">{{ $item->category }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-red-600">{{ $item->quantity_in_stock }}</p>
                    <p class="text-xs text-gray-400">/ {{ $item->reorder_level }} {{ $item->unit }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">All stock levels are healthy ✓</p>
        @endforelse
    </div>

    {{-- Recent Issuances (spans 2 cols) --}}
    <div class="card lg:col-span-2">
        <h3 class="font-semibold text-gray-700 mb-4">Recent Issuances</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="table-th pl-0">Item</th>
                        <th class="table-th">Issued To</th>
                        <th class="table-th">Date</th>
                        <th class="table-th">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentIssuances as $i)
                    <tr>
                        <td class="table-td pl-0 font-medium">{{ $i->issuable?->name ?? 'N/A' }}</td>
                        <td class="table-td">{{ $i->recipient?->name ?? 'N/A' }}</td>
                        <td class="table-td text-gray-500">{{ $i->issue_date->format('d M Y') }}</td>
                        <td class="table-td">
                            @php
                                $statusClass = match($i->status) {
                                    'Issued'   => 'bg-amber-100 text-amber-700',
                                    'Returned' => 'bg-green-100 text-green-700',
                                    default    => 'bg-red-100 text-red-700',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $i->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-td text-center text-gray-400 py-6">No issuances yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Asset Doughnut Chart
new Chart(document.getElementById('assetChart'), {
    type: 'doughnut',
    data: {
        labels: @json($assetChartData->keys()),
        datasets: [{
            data: @json($assetChartData->values()),
            backgroundColor: ['#3B82F6', '#F59E0B', '#EF4444'],
            borderWidth: 0,
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        cutout: '65%',
    }
});

// Consumable Bar Chart
new Chart(document.getElementById('consumableChart'), {
    type: 'bar',
    data: {
        labels: @json($consumableChartData->keys()),
        datasets: [{
            label: 'Units in Stock',
            data: @json($consumableChartData->values()),
            backgroundColor: '#10B981',
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } },
    }
});
</script>
@endpush