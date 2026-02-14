@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<!-- Stat Cards -->
<div class="stat-grid">
    <div class="sc blue">
        <div class="sc-ico">🖥️</div>
        <div class="sc-val">{{ $stats['total_assets'] }}</div>
        <div class="sc-lbl">Total Assets</div>
    </div>
    <div class="sc teal">
        <div class="sc-ico">📦</div>
        <div class="sc-val">{{ $stats['total_consumables'] }}</div>
        <div class="sc-lbl">Consumables</div>
    </div>
    <div class="sc amber">
        <div class="sc-ico">📤</div>
        <div class="sc-val">{{ $stats['issued_assets'] }}</div>
        <div class="sc-lbl">Items Issued</div>
    </div>
    <div class="sc red">
        <div class="sc-ico">⚠️</div>
        <div class="sc-val">{{ $stats['low_stock_count'] }}</div>
        <div class="sc-lbl">Low Stock</div>
    </div>
    <div class="sc violet">
        <div class="sc-ico">👤</div>
        <div class="sc-val">{{ $stats['total_staff'] }}</div>
        <div class="sc-lbl">Staff Members</div>
    </div>
    <div class="sc rose">
        <div class="sc-ico">🔴</div>
        <div class="sc-val">{{ $stats['overdue'] }}</div>
        <div class="sc-lbl">Overdue Items</div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockItems->count() > 0)
<div class="low-bar">
    <span style="font-size:18px">⚠️</span>
    <span><strong>Low Stock:</strong> {{ $lowStockItems->pluck('name')->join(', ') }} — need restocking</span>
    @if(in_array(auth()->user()->role,['admin','storekeeper']))
        <a href="{{ route('consumables.index') }}" class="btn btn-d btn-sm" style="margin-left:auto">View →</a>
    @endif
</div>
@endif

<!-- Charts -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px">
    <div class="card">
        <div class="card-hdr"><span class="card-title">Asset Status</span></div>
        <div class="card-body" style="display:flex;justify-content:center;align-items:center;min-height:210px">
            <canvas id="assetChart" style="max-height:190px"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-hdr"><span class="card-title">Stock by Category</span></div>
        <div class="card-body" style="min-height:210px">
            <canvas id="consChart" style="max-height:190px"></canvas>
        </div>
    </div>
</div>

<!-- Recent Issuances -->
<div class="card">
    <div class="card-hdr">
        <span class="card-title">Recent Issuances</span>
        @if(auth()->user()->role !== 'staff')
            <a href="{{ route('issuances.index') }}" class="btn btn-s btn-sm">View All</a>
        @endif
    </div>
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Item</th>
                    <th>Issued To</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentIssuances as $i)
                <tr>
                    <td style="font-family:monospace;font-size:.76rem;color:#64748b">{{ $i->issuance_code }}</td>
                    <td style="font-weight:600">{{ $i->issuable?->name ?? '—' }}</td>
                    <td>{{ $i->recipient?->name ?? '—' }}</td>
                    <td style="color:#64748b">{{ $i->issue_date->format('d M Y') }}</td>
                    <td>
                        @if($i->status === 'Issued')   <span class="badge ba">Issued</span>
                        @elseif($i->status === 'Returned') <span class="badge bg">Returned</span>
                        @else                          <span class="badge br">Overdue</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8">No issuances yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('assetChart'),{
    type:'doughnut',
    data:{
        labels:@json($assetChartData->keys()),
        datasets:[{data:@json($assetChartData->values()),
            backgroundColor:['#14b8a6','#f59e0b','#ef4444','#8b5cf6'],
            borderWidth:0,hoverOffset:6}]
    },
    options:{responsive:true,plugins:{legend:{position:'bottom',
        labels:{font:{family:'Inter',size:12},padding:14,usePointStyle:true}}},
        cutout:'65%'}
});
new Chart(document.getElementById('consChart'),{
    type:'bar',
    data:{
        labels:@json($consumableChartData->keys()),
        datasets:[{label:'Units in Stock',data:@json($consumableChartData->values()),
            backgroundColor:'rgba(13,148,136,.15)',borderColor:'#0d9488',
            borderWidth:2,borderRadius:6,borderSkipped:false}]
    },
    options:{responsive:true,plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{font:{family:'Inter'}}},
                x:{grid:{display:false},ticks:{font:{family:'Inter'}}}}}
});
</script>
@endpush