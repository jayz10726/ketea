@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<style>
/* Ultra colorful stat cards with gradients */
.sc-ultra{background:linear-gradient(135deg,var(--c1),var(--c2));
    border:none;color:#fff;box-shadow:0 8px 32px var(--shadow)}
.sc-ultra::after{display:none}
.sc-ultra .sc-ico{background:rgba(255,255,255,.2);backdrop-filter:blur(10px)}
.sc-ultra .sc-val{color:#fff}
.sc-ultra .sc-lbl{color:rgba(255,255,255,.85)}
.sc-ultra:hover{transform:translateY(-3px);box-shadow:0 12px 40px var(--shadow)}

.sc1{--c1:#3b82f6;--c2:#1d4ed8;--shadow:rgba(59,130,246,.4)}
.sc2{--c1:#0d9488;--c2:#0f766e;--shadow:rgba(13,148,136,.4)}
.sc3{--c1:#f59e0b;--c2:#d97706;--shadow:rgba(245,158,11,.4)}
.sc4{--c1:#ef4444;--c2:#dc2626;--shadow:rgba(239,68,68,.4)}
.sc5{--c1:#8b5cf6;--c2:#7c3aed;--shadow:rgba(139,92,246,.4)}
.sc6{--c1:#ec4899;--c2:#db2777;--shadow:rgba(236,72,153,.4)}

/* Animated gradient background */
@keyframes gradFlow{
    0%,100%{background-position:0% 50%}
    50%{background-position:100% 50%}
}
.grad-bg{background:linear-gradient(135deg,#667eea 0%,#764ba2 25%,#f093fb 50%,#4facfe 75%,#00f2fe 100%);
    background-size:400% 400%;animation:gradFlow 15s ease infinite}
</style>

<!-- Colorful Stat Cards with Gradients -->
<div class="stat-grid">
    <div class="sc sc-ultra sc1">
        <div class="sc-ico">🖥️</div>
        <div class="sc-val">{{ $stats['total_assets'] }}</div>
        <div class="sc-lbl">Total Assets</div>
    </div>
    <div class="sc sc-ultra sc2">
        <div class="sc-ico">📦</div>
        <div class="sc-val">{{ $stats['total_consumables'] }}</div>
        <div class="sc-lbl">Consumables</div>
    </div>
    <div class="sc sc-ultra sc3">
        <div class="sc-ico">📤</div>
        <div class="sc-val">{{ $stats['issued_assets'] }}</div>
        <div class="sc-lbl">Items Issued</div>
    </div>
    <div class="sc sc-ultra sc4">
        <div class="sc-ico">⚠️</div>
        <div class="sc-val">{{ $stats['low_stock_count'] }}</div>
        <div class="sc-lbl">Low Stock Alerts</div>
    </div>
    <div class="sc sc-ultra sc5">
        <div class="sc-ico">👥</div>
        <div class="sc-val">{{ $stats['total_staff'] }}</div>
        <div class="sc-lbl">Staff Members</div>
    </div>
    <div class="sc sc-ultra sc6">
        <div class="sc-ico">🔴</div>
        <div class="sc-val">{{ $stats['overdue'] }}</div>
        <div class="sc-lbl">Overdue Returns</div>
    </div>
</div>

<!-- Low Stock Alert with Animation -->
@if($lowStockItems->count() > 0)
<div class="low-bar" style="background:linear-gradient(135deg,#fef2f2,#fee2e2,#fef2f2);
    background-size:200% 100%;animation:pulse 3s ease-in-out infinite;
    box-shadow:0 4px 12px rgba(239,68,68,.2)">
    <span style="font-size:20px;animation:shake .5s infinite">⚠️</span>
    <span><strong>{{ $lowStockItems->count() }} items critically low:</strong>
        {{ $lowStockItems->pluck('name')->take(3)->join(', ') }}
        @if($lowStockItems->count() > 3) and {{ $lowStockItems->count() - 3 }} more @endif
    </span>
    @if(in_array(auth()->user()->role,['admin','storekeeper']))
        <a href="{{ route('consumables.index') }}"
           style="margin-left:auto;background:#dc2626;color:#fff;padding:6px 14px;
               border-radius:20px;font-size:.78rem;font-weight:700;text-decoration:none;
               box-shadow:0 2px 8px rgba(220,38,38,.3)">
            Restock Now →
        </a>
    @endif
</div>
@endif

<!-- Charts with Colorful Cards -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="card" style="border:2px solid #e0e7ff;box-shadow:0 4px 16px rgba(99,102,241,.15)">
        <div class="card-hdr" style="background:linear-gradient(135deg,#818cf8,#6366f1)">
            <span class="card-title" style="color:#fff;display:flex;align-items:center;gap:8px">
                📊 Asset Status Distribution
            </span>
        </div>
        <div class="card-body" style="display:flex;justify-content:center;align-items:center;min-height:240px">
            <canvas id="assetChart" style="max-height:220px"></canvas>
        </div>
    </div>

    <div class="card" style="border:2px solid #d1fae5;box-shadow:0 4px 16px rgba(16,185,129,.15)">
        <div class="card-hdr" style="background:linear-gradient(135deg,#34d399,#10b981)">
            <span class="card-title" style="color:#fff;display:flex;align-items:center;gap:8px">
                📈 Stock Levels by Category
            </span>
        </div>
        <div class="card-body" style="min-height:240px">
            <canvas id="consChart" style="max-height:220px"></canvas>
        </div>
    </div>
</div>

<!-- Recent Issuances with Rainbow Headers -->
<div class="card" style="border:2px solid #e9d5ff;box-shadow:0 4px 16px rgba(168,85,247,.12)">
    <div class="card-hdr grad-bg">
        <span class="card-title" style="color:#fff;text-shadow:0 2px 4px rgba(0,0,0,.2);
            display:flex;align-items:center;gap:8px">
            ✨ Recent Item Issuances
        </span>
        @if(auth()->user()->role !== 'staff')
            <a href="{{ route('issuances.index') }}"
               style="background:rgba(255,255,255,.25);backdrop-filter:blur(10px);
                   color:#fff;padding:5px 12px;border-radius:20px;text-decoration:none;
                   font-size:.78rem;font-weight:600;border:1px solid rgba(255,255,255,.3)">
                View All →
            </a>
        @endif
    </div>
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr style="background:linear-gradient(135deg,#f3f4f6,#e5e7eb)">
                    <th>Code</th><th>Item</th><th>Issued To</th>
                    <th>Date</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentIssuances as $i)
                <tr style="transition:all .2s">
                    <td style="font-family:monospace;font-size:.75rem;color:#7c3aed;font-weight:700">
                        {{ $i->issuance_code }}
                    </td>
                    <td style="font-weight:600;color:#1e293b">
                        {{ $i->issuable?->name ?? '—' }}
                    </td>
                    <td>{{ $i->recipient?->name ?? '—' }}</td>
                    <td style="color:#64748b">{{ $i->issue_date->format('d M Y') }}</td>
                    <td>
                        @if($i->status === 'Issued')
                            <span style="background:linear-gradient(135deg,#fef3c7,#fde68a);
                                color:#92400e;padding:4px 10px;border-radius:20px;font-size:.7rem;
                                font-weight:700;display:inline-flex;align-items:center;gap:4px">
                                <span style="width:6px;height:6px;border-radius:50%;background:#f59e0b"></span>
                                Issued
                            </span>
                        @elseif($i->status === 'Returned')
                            <span style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);
                                color:#065f46;padding:4px 10px;border-radius:20px;font-size:.7rem;
                                font-weight:700;display:inline-flex;align-items:center;gap:4px">
                                <span style="width:6px;height:6px;border-radius:50%;background:#10b981"></span>
                                Returned
                            </span>
                        @else
                            <span style="background:linear-gradient(135deg,#fee2e2,#fecaca);
                                color:#7f1d1d;padding:4px 10px;border-radius:20px;font-size:.7rem;
                                font-weight:700;display:inline-flex;align-items:center;gap:4px">
                                <span style="width:6px;height:6px;border-radius:50%;background:#ef4444;
                                    animation:blink 1s infinite"></span>
                                Overdue
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:#94a3b8">
                        <div style="font-size:3rem;margin-bottom:12px;opacity:.4">📋</div>
                        <div style="font-size:.9rem">No issuances recorded yet.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Colorful Asset Chart
new Chart(document.getElementById('assetChart'),{
    type:'doughnut',
    data:{
        labels:@json($assetChartData->keys()),
        datasets:[{
            data:@json($assetChartData->values()),
            backgroundColor:['#10b981','#f59e0b','#ef4444','#8b5cf6','#3b82f6'],
            borderWidth:4,
            borderColor:'#fff',
            hoverOffset:12,
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{
                position:'bottom',
                labels:{
                    font:{family:'Inter',size:13,weight:'600'},
                    padding:18,
                    usePointStyle:true,
                    pointStyle:'circle'
                }
            }
        },
        cutout:'68%',
    }
});

// Vibrant Bar Chart
new Chart(document.getElementById('consChart'),{
    type:'bar',
    data:{
        labels:@json($consumableChartData->keys()),
        datasets:[{
            label:'Units in Stock',
            data:@json($consumableChartData->values()),
            backgroundColor:[
                'rgba(59,130,246,.7)',
                'rgba(16,185,129,.7)',
                'rgba(245,158,11,.7)',
                'rgba(139,92,246,.7)',
                'rgba(236,72,153,.7)'
            ],
            borderColor:[
                '#3b82f6','#10b981','#f59e0b','#8b5cf6','#ec4899'
            ],
            borderWidth:3,
            borderRadius:8,
            borderSkipped:false,
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{display:false}
        },
        scales:{
            y:{
                beginAtZero:true,
                grid:{color:'#f1f5f9'},
                ticks:{font:{family:'Inter',weight:'600'}}
            },
            x:{
                grid:{display:false},
                ticks:{font:{family:'Inter',weight:'600'}}
            }
        }
    }
});
</script>
@endpush