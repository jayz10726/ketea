@extends('layouts.app')
@section('title','Consumables')
@section('content')

<div x-data="{ addOpen:false, editOpen:false, ec:{} }">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <p style="font-size:.855rem;color:#64748b">Track stock levels — red items are below reorder level</p>
    <button @click="addOpen=true" class="btn btn-p">+ Add Item</button>
</div>

@php $lowCount = $consumables->filter(fn($c)=>$c->isLowStock())->count(); @endphp
@if($lowCount > 0)
<div class="low-bar">
    ⚠️ <strong>{{ $lowCount }} item(s)</strong> are below reorder level and need restocking immediately.
</div>
@endif

<form method="GET" action="{{ route('consumables.index') }}">
    <div class="fbar">
        <div class="sw">
            <span>🔍</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search consumables…" class="si">
        </div>
        <select name="category" class="fsel">
            <option value="">All Categories</option>
            <option {{ request('category')==='Stationery'  ?'selected':'' }}>Stationery</option>
            <option {{ request('category')==='Consumables' ?'selected':'' }}>Consumables</option>
            <option {{ request('category')==='Cleaning'    ?'selected':'' }}>Cleaning</option>
        </select>
        <button type="submit" class="btn btn-s">Filter</button>
        @if(request()->hasAny(['search','category']))
            <a href="{{ route('consumables.index') }}" class="btn btn-s">✕ Clear</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Category</th>
                    <th>In Stock</th><th>Reorder Level</th><th>Unit</th>
                    <th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consumables as $c)
                <tr style="{{ $c->isLowStock() ? 'background:#fff5f5' : '' }}">
                    <td style="color:#94a3b8;font-size:.78rem">{{ $c->id }}</td>
                    <td style="font-weight:600;color:#1e293b">{{ $c->name }}</td>
                    <td><span class="badge bs">{{ $c->category }}</span></td>
                    <td>
                        <span style="font-family:monospace;font-size:.9rem;font-weight:700;
                            color:{{ $c->isLowStock() ? '#dc2626' : '#0d9488' }}">
                            {{ $c->quantity_in_stock }}
                        </span>
                    </td>
                    <td style="color:#64748b">{{ $c->reorder_level }}</td>
                    <td style="color:#64748b">{{ $c->unit }}</td>
                    <td>
                        @if($c->isLowStock())
                            <span class="badge br">⚠ Low Stock</span>
                        @else
                            <span class="badge bg">OK</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button @click="editOpen=true; ec={{ json_encode($c) }}"
                                    class="btn btn-s btn-sm">✏️</button>
                            <form method="POST" action="{{ route('consumables.destroy',$c) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($c->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-d btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:52px;color:#94a3b8">
                        <div style="font-size:2rem;margin-bottom:8px">📦</div>
                        No consumables found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag">{{ $consumables->links() }}</div>
</div>

<!-- ADD MODAL -->
<div class="mo" x-show="addOpen" x-cloak @click.self="addOpen=false">
    <div class="md">
        <div class="md-hdr">
            <span class="md-title">➕ Add Consumable</span>
            <button class="md-x" @click="addOpen=false">✕</button>
        </div>
        <form method="POST" action="{{ route('consumables.store') }}">
            @csrf
            <div class="md-body">
                <div class="f2">
                    <div class="fg">
                        <label class="fl">Name <em>*</em></label>
                        <input type="text" name="name" class="fi" required placeholder="e.g. A4 Paper">
                    </div>
                    <div class="fg">
                        <label class="fl">Category <em>*</em></label>
                        <select name="category" class="fse" required>
                            <option value="">Select…</option>
                            <option>Stationery</option>
                            <option>Consumables</option>
                            <option>Cleaning</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Quantity in Stock <em>*</em></label>
                        <input type="number" name="quantity_in_stock" class="fi" required min="0" value="0">
                    </div>
                    <div class="fg">
                        <label class="fl">Reorder Level <em>*</em></label>
                        <input type="number" name="reorder_level" class="fi" required min="0" value="5">
                    </div>
                    <div class="fg">
                        <label class="fl">Unit <em>*</em></label>
                        <input type="text" name="unit" class="fi" required placeholder="e.g. reams, pcs, boxes">
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Notes</label>
                    <textarea name="notes" class="ft" placeholder="Any notes…"></textarea>
                </div>
            </div>
            <div class="md-foot">
                <button type="button" @click="addOpen=false" class="btn btn-s">Cancel</button>
                <button type="submit" class="btn btn-p">Save Item</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="mo" x-show="editOpen" x-cloak @click.self="editOpen=false">
    <div class="md">
        <div class="md-hdr">
            <span class="md-title">✏️ Edit Consumable</span>
            <button class="md-x" @click="editOpen=false">✕</button>
        </div>
        <form method="POST" :action="`/consumables/${ec.id}`">
            @csrf @method('PUT')
            <div class="md-body">
                <div class="f2">
                    <div class="fg">
                        <label class="fl">Name <em>*</em></label>
                        <input type="text" name="name" class="fi" :value="ec.name" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Category <em>*</em></label>
                        <select name="category" class="fse" required>
                            <option :selected="ec.category==='Stationery'">Stationery</option>
                            <option :selected="ec.category==='Consumables'">Consumables</option>
                            <option :selected="ec.category==='Cleaning'">Cleaning</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Quantity in Stock <em>*</em></label>
                        <input type="number" name="quantity_in_stock" class="fi" :value="ec.quantity_in_stock" required min="0">
                    </div>
                    <div class="fg">
                        <label class="fl">Reorder Level <em>*</em></label>
                        <input type="number" name="reorder_level" class="fi" :value="ec.reorder_level" required min="0">
                    </div>
                    <div class="fg">
                        <label class="fl">Unit <em>*</em></label>
                        <input type="text" name="unit" class="fi" :value="ec.unit" required>
                    </div>
                </div>
            </div>
            <div class="md-foot">
                <button type="button" @click="editOpen=false" class="btn btn-s">Cancel</button>
                <button type="submit" class="btn btn-p">Update Item</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection