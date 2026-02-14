@extends('layouts.app')
@section('title','Reports')
@section('content')

<!-- Export buttons -->
<div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap">
    <a href="{{ route('reports.export',['type'=>'stock']) }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;
           background:linear-gradient(135deg,#0d9488,#0f766e);color:#fff;border-radius:9px;
           text-decoration:none;font-size:.855rem;font-weight:600;
           box-shadow:0 4px 14px rgba(13,148,136,.3)">
        📥 Export Stock CSV
    </a>
    <a href="{{ route('reports.export',['type'=>'assets']) }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;
           background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:9px;
           text-decoration:none;font-size:.855rem;font-weight:600;
           box-shadow:0 4px 14px rgba(37,99,235,.3)">
        📥 Export Assets CSV
    </a>
    <a href="{{ route('reports.export',['type'=>'low']) }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;
           background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border-radius:9px;
           text-decoration:none;font-size:.855rem;font-weight:600;
           box-shadow:0 4px 14px rgba(220,38,38,.3)">
        📥 Export Low Stock CSV
    </a>
</div>

<!-- Asset Status Summary -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:24px">
    @foreach($assetsByStatus as $status => $count)
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px;text-align:center">
        <div style="font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;
            color:{{ $status==='Available'?'#0d9488':($status==='Issued'?'#d97706':'#dc2626') }}">
            {{ $count }}
        </div>
        <div style="font-size:.78rem;color:#64748b;margin-top:4px">{{ $status }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">

    <!-- Low Stock -->
    <div class="card">
        <div class="card-hdr">
            <span class="card-title" style="color:#dc2626">⚠️ Low Stock Items</span>
            <span style="font-size:.78rem;color:#94a3b8">{{ $lowStock->count() }} items</span>
        </div>
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr><th>Item</th><th>Stock</th><th>Reorder</th><th>Unit</th></tr>
                </thead>
                <tbody>
                    @forelse($lowStock as $c)
                    <tr>
                        <td style="font-weight:600">{{ $c->name }}</td>
                        <td style="font-family:monospace;color:#dc2626;font-weight:700">{{ $c->quantity_in_stock }}</td>
                        <td style="color:#64748b">{{ $c->reorder_level }}</td>
                        <td style="color:#94a3b8">{{ $c->unit }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:32px;color:#22c55e">
                            ✓ All stock levels healthy!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Issuances -->
    <div class="card">
        <div class="card-hdr">
            <span class="card-title">📋 Recent Issuance History</span>
        </div>
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr><th>Item</th><th>Issued To</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($recentIssuances as $i)
                    <tr>
                        <td style="font-weight:600">{{ $i->issuable?->name ?? '—' }}</td>
                        <td>{{ $i->recipient?->name ?? '—' }}</td>
                        <td style="color:#64748b;font-size:.78rem">{{ $i->issue_date->format('d M Y') }}</td>
                        <td>
                            @if($i->status==='Issued')   <span class="badge ba">Issued</span>
                            @elseif($i->status==='Returned') <span class="badge bg">Returned</span>
                            @else                        <span class="badge br">Overdue</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:32px;color:#94a3b8">No issuances.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection