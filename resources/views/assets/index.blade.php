@extends('layouts.app')
@section('title','Assets')
@section('content')

<div x-data="{ addOpen:false, editOpen:false, ea:{} }">

<!-- Top bar -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <p style="font-size:.855rem;color:#64748b">Manage furniture, electronics and equipment</p>
    <button @click="addOpen=true" class="btn btn-p">+ Add Asset</button>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('assets.index') }}">
    <div class="fbar">
        <div class="sw">
            <span>🔍</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name or code…" class="si">
        </div>
        <select name="category" class="fsel">
            <option value="">All Categories</option>
            <option {{ request('category')==='Electronics' ?'selected':'' }}>Electronics</option>
            <option {{ request('category')==='Furniture'   ?'selected':'' }}>Furniture</option>
            <option {{ request('category')==='Equipment'   ?'selected':'' }}>Equipment</option>
        </select>
        <select name="status" class="fsel">
            <option value="">All Status</option>
            <option {{ request('status')==='Available'         ?'selected':'' }}>Available</option>
            <option {{ request('status')==='Issued'            ?'selected':'' }}>Issued</option>
            <option {{ request('status')==='Under Maintenance' ?'selected':'' }}>Under Maintenance</option>
        </select>
        <button type="submit" class="btn btn-s">Filter</button>
        @if(request()->hasAny(['search','category','status']))
            <a href="{{ route('assets.index') }}" class="btn btn-s">✕ Clear</a>
        @endif
    </div>
</form>

<!-- Table -->
<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th><th>Name</th><th>Category</th>
                    <th>Condition</th><th>Location</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td style="font-family:monospace;font-size:.78rem;color:#0d9488;font-weight:600">
                        {{ $asset->asset_code }}
                    </td>
                    <td>
                        <div style="font-weight:600;color:#1e293b">{{ $asset->name }}</div>
                        @if($asset->serial_number)
                            <div style="font-size:.7rem;color:#94a3b8">S/N: {{ $asset->serial_number }}</div>
                        @endif
                    </td>
                    <td><span class="badge bb">{{ $asset->category }}</span></td>
                    <td>
                        @if($asset->condition==='New')     <span class="badge bg">New</span>
                        @elseif($asset->condition==='Good')<span class="badge bb">Good</span>
                        @else                              <span class="badge br">Damaged</span>
                        @endif
                    </td>
                    <td style="color:#64748b">{{ $asset->location ?? '—' }}</td>
                    <td>
                        @if($asset->status==='Available')        <span class="badge bg">Available</span>
                        @elseif($asset->status==='Issued')        <span class="badge ba">Issued</span>
                        @else                                     <span class="badge br">Maintenance</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button @click="editOpen=true; ea={{ json_encode($asset) }}"
                                    class="btn btn-s btn-sm">✏️ Edit</button>
                            @if($asset->status !== 'Issued')
                            <form method="POST" action="{{ route('assets.destroy',$asset) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($asset->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-d btn-sm">🗑</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:52px;color:#94a3b8">
                        <div style="font-size:2rem;margin-bottom:8px">📋</div>
                        No assets found. Add your first asset!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag">{{ $assets->links() }}</div>
</div>

<!-- ADD MODAL -->
<div class="mo" x-show="addOpen" x-cloak @click.self="addOpen=false">
    <div class="md">
        <div class="md-hdr">
            <span class="md-title">➕ Add New Asset</span>
            <button class="md-x" @click="addOpen=false">✕</button>
        </div>
        <form method="POST" action="{{ route('assets.store') }}">
            @csrf
            <div class="md-body">
                <div class="f2">
                    <div class="fg">
                        <label class="fl">Name <em>*</em></label>
                        <input type="text" name="name" class="fi" required placeholder="e.g. Dell Laptop">
                    </div>
                    <div class="fg">
                        <label class="fl">Asset Code</label>
                        <input type="text" name="asset_code" class="fi" placeholder="Auto-generated">
                    </div>
                    <div class="fg">
                        <label class="fl">Category <em>*</em></label>
                        <select name="category" class="fse" required>
                            <option value="">Select…</option>
                            <option>Electronics</option>
                            <option>Furniture</option>
                            <option>Equipment</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Condition <em>*</em></label>
                        <select name="condition" class="fse" required>
                            <option>New</option><option>Good</option><option>Damaged</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Serial Number</label>
                        <input type="text" name="serial_number" class="fi" placeholder="Optional">
                    </div>
                    <div class="fg">
                        <label class="fl">Location</label>
                        <input type="text" name="location" class="fi" placeholder="e.g. IT Room">
                    </div>
                    <div class="fg">
                        <label class="fl">Status <em>*</em></label>
                        <select name="status" class="fse" required>
                            <option>Available</option><option>Under Maintenance</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Purchase Date</label>
                        <input type="date" name="purchase_date" class="fi">
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Notes</label>
                    <textarea name="notes" class="ft" placeholder="Any additional notes…"></textarea>
                </div>
            </div>
            <div class="md-foot">
                <button type="button" @click="addOpen=false" class="btn btn-s">Cancel</button>
                <button type="submit" class="btn btn-p">Save Asset</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="mo" x-show="editOpen" x-cloak @click.self="editOpen=false">
    <div class="md">
        <div class="md-hdr">
            <span class="md-title">✏️ Edit Asset</span>
            <button class="md-x" @click="editOpen=false">✕</button>
        </div>
        <form method="POST" :action="`/assets/${ea.id}`">
            @csrf @method('PUT')
            <div class="md-body">
                <div class="f2">
                    <div class="fg">
                        <label class="fl">Name <em>*</em></label>
                        <input type="text" name="name" class="fi" :value="ea.name" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Asset Code</label>
                        <input type="text" name="asset_code" class="fi" :value="ea.asset_code">
                    </div>
                    <div class="fg">
                        <label class="fl">Category <em>*</em></label>
                        <select name="category" class="fse" required>
                            <option :selected="ea.category==='Electronics'">Electronics</option>
                            <option :selected="ea.category==='Furniture'">Furniture</option>
                            <option :selected="ea.category==='Equipment'">Equipment</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Condition <em>*</em></label>
                        <select name="condition" class="fse" required>
                            <option :selected="ea.condition==='New'">New</option>
                            <option :selected="ea.condition==='Good'">Good</option>
                            <option :selected="ea.condition==='Damaged'">Damaged</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Location</label>
                        <input type="text" name="location" class="fi" :value="ea.location">
                    </div>
                    <div class="fg">
                        <label class="fl">Status <em>*</em></label>
                        <select name="status" class="fse" required>
                            <option :selected="ea.status==='Available'">Available</option>
                            <option :selected="ea.status==='Issued'">Issued</option>
                            <option :selected="ea.status==='Under Maintenance'">Under Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="md-foot">
                <button type="button" @click="editOpen=false" class="btn btn-s">Cancel</button>
                <button type="submit" class="btn btn-p">Update Asset</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection