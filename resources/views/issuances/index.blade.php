@extends('layouts.app')
@section('title','Issuances')
@section('content')

<div x-data="{ issueOpen:false, itemType:'asset' }">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <p style="font-size:.855rem;color:#64748b">Issue and track assets & consumables to staff members</p>
    <button @click="issueOpen=true" class="btn btn-p">+ Issue Item</button>
</div>

<!-- Status filter -->
<form method="GET" action="{{ route('issuances.index') }}">
    <div class="fbar">
        <select name="status" class="fsel">
            <option value="">All Status</option>
            <option {{ request('status')==='Issued'   ?'selected':'' }}>Issued</option>
            <option {{ request('status')==='Returned' ?'selected':'' }}>Returned</option>
            <option {{ request('status')==='Overdue'  ?'selected':'' }}>Overdue</option>
        </select>
        <button type="submit" class="btn btn-s">Filter</button>
        @if(request('status'))
            <a href="{{ route('issuances.index') }}" class="btn btn-s">✕ Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th><th>Type</th><th>Item</th><th>Qty</th>
                    <th>Issued To</th><th>Issue Date</th><th>Return Due</th>
                    <th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issuances as $i)
                <tr>
                    <td style="font-family:monospace;font-size:.73rem;color:#64748b">{{ $i->issuance_code }}</td>
                    <td>
                        @if(str_contains($i->issuable_type,'Asset'))
                            <span class="badge bb">Asset</span>
                        @else
                            <span class="badge bp">Consumable</span>
                        @endif
                    </td>
                    <td style="font-weight:600">{{ $i->issuable?->name ?? '—' }}</td>
                    <td style="font-family:monospace">{{ $i->quantity }}</td>
                    <td>{{ $i->recipient?->name ?? '—' }}</td>
                    <td style="color:#64748b">{{ $i->issue_date->format('d M Y') }}</td>
                    <td style="color:#64748b">{{ $i->expected_return_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($i->status==='Issued')   <span class="badge ba">Issued</span>
                        @elseif($i->status==='Returned') <span class="badge bg">Returned</span>
                        @else                        <span class="badge br">Overdue</span>
                        @endif
                    </td>
                    <td>
                        @if($i->status === 'Issued')
                        <form method="POST" action="{{ route('issuances.return',$i) }}"
                              onsubmit="return confirm('Mark as returned?')">
                            @csrf
                            <button type="submit" class="btn btn-s btn-sm">↩ Return</button>
                        </form>
                        @else
                            <span style="color:#94a3b8;font-size:.78rem">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:52px;color:#94a3b8">
                        <div style="font-size:2rem;margin-bottom:8px">📋</div>
                        No issuances recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag">{{ $issuances->links() }}</div>
</div>

<!-- ISSUE MODAL -->
<div class="mo" x-show="issueOpen" x-cloak @click.self="issueOpen=false">
    <div class="md" style="max-width:520px">
        <div class="md-hdr">
            <span class="md-title">📤 Issue Item to Staff</span>
            <button class="md-x" @click="issueOpen=false">✕</button>
        </div>
        <form method="POST" action="{{ route('issuances.store') }}">
            @csrf
            <div class="md-body">

                <!-- Item Type Toggle -->
                <div class="fg">
                    <label class="fl">Item Type <em>*</em></label>
                    <div style="display:flex;gap:10px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;
                            flex:1;padding:10px 14px;border:2px solid;border-radius:8px;transition:all .15s"
                            :style="itemType==='asset' ? 'border-color:#0d9488;background:#f0fdfa' : 'border-color:#e2e8f0'">
                            <input type="radio" name="issuable_type" value="asset"
                                   x-model="itemType" style="accent-color:#0d9488">
                            <span style="font-size:.855rem;font-weight:600">🖥️ Asset</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;
                            flex:1;padding:10px 14px;border:2px solid;border-radius:8px;transition:all .15s"
                            :style="itemType==='consumable' ? 'border-color:#8b5cf6;background:#f5f3ff' : 'border-color:#e2e8f0'">
                            <input type="radio" name="issuable_type" value="consumable"
                                   x-model="itemType" style="accent-color:#8b5cf6">
                            <span style="font-size:.855rem;font-weight:600">📦 Consumable</span>
                        </label>
                    </div>
                </div>

                <!-- Asset select -->
                <div class="fg" x-show="itemType==='asset'">
                    <label class="fl">Select Asset <em>*</em></label>
                    <select name="issuable_id" class="fse">
                        <option value="">— Choose available asset —</option>
                        @foreach($assets as $a)
                            <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->asset_code }}) — {{ $a->location }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Consumable select -->
                <div class="fg" x-show="itemType==='consumable'">
                    <label class="fl">Select Consumable <em>*</em></label>
                    <select name="issuable_id" class="fse">
                        <option value="">— Choose consumable —</option>
                        @foreach($consumables as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->quantity_in_stock }} {{ $c->unit }} left</option>
                        @endforeach
                    </select>
                </div>

                <div class="f2">
                    <div class="fg">
                        <label class="fl">Issue To <em>*</em></label>
                        <select name="issued_to" class="fse" required>
                            <option value="">— Select staff —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->department ?? $u->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg" x-show="itemType==='consumable'">
                        <label class="fl">Quantity <em>*</em></label>
                        <input type="number" name="quantity" class="fi" value="1" min="1">
                    </div>
                </div>

                <div class="f2">
                    <div class="fg">
                        <label class="fl">Issue Date <em>*</em></label>
                        <input type="date" name="issue_date" class="fi"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Expected Return</label>
                        <input type="date" name="expected_return_date" class="fi">
                    </div>
                </div>

                <div class="fg">
                    <label class="fl">Purpose</label>
                    <input type="text" name="purpose" class="fi" placeholder="Reason for issuance…">
                </div>
            </div>
            <div class="md-foot">
                <button type="button" @click="issueOpen=false" class="btn btn-s">Cancel</button>
                <button type="submit" class="btn btn-p">Issue Item</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection