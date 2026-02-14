@extends('layouts.app')
@section('title','Users')
@section('content')

<div x-data="{ addOpen:false }">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <p style="font-size:.855rem;color:#64748b">Manage system users and access roles</p>
    <button @click="addOpen=true" class="btn btn-p">+ Add User</button>
</div>

<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th><th>Email</th><th>Role</th>
                    <th>Department</th><th>Joined</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                                background:linear-gradient(135deg,#0d9488,#0891b2);
                                display:grid;place-items:center;font-family:'Syne',sans-serif;
                                font-size:.85rem;font-weight:700;color:#fff">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div style="font-weight:600;color:#1e293b">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td style="color:#64748b">{{ $user->email }}</td>
                    <td>
                        @if($user->role==='admin')
                            <span class="badge bg">Admin</span>
                        @elseif($user->role==='storekeeper')
                            <span class="badge ba">Storekeeper</span>
                        @else
                            <span class="badge bb">Staff</span>
                        @endif
                    </td>
                    <td style="color:#64748b">{{ $user->department ?? '—' }}</td>
                    <td style="color:#94a3b8;font-size:.78rem">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy',$user) }}"
                              onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-d btn-sm">🗑 Delete</button>
                        </form>
                        @else
                            <span style="font-size:.78rem;color:#94a3b8">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:52px;color:#94a3b8">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pag">{{ $users->links() }}</div>
</div>

<!-- ADD USER MODAL -->
<div class="mo" x-show="addOpen" x-cloak @click.self="addOpen=false">
    <div class="md">
        <div class="md-hdr">
            <span class="md-title">👤 Add New User</span>
            <button class="md-x" @click="addOpen=false">✕</button>
        </div>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="md-body">
                <div class="f2">
                    <div class="fg">
                        <label class="fl">Full Name <em>*</em></label>
                        <input type="text" name="name" class="fi" required placeholder="John Doe">
                    </div>
                    <div class="fg">
                        <label class="fl">Email Address <em>*</em></label>
                        <input type="email" name="email" class="fi" required placeholder="john@ketea.com">
                    </div>
                    <div class="fg">
                        <label class="fl">Password <em>*</em></label>
                        <input type="password" name="password" class="fi" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Confirm Password <em>*</em></label>
                        <input type="password" name="password_confirmation" class="fi" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Role <em>*</em></label>
                        <select name="role" class="fse" required>
                            <option value="">Select role…</option>
                            <option value="admin">Admin</option>
                            <option value="storekeeper">Storekeeper</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Department</label>
                        <input type="text" name="department" class="fi" placeholder="e.g. Finance">
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Phone</label>
                    <input type="text" name="phone" class="fi" placeholder="+254 7xx xxx xxx">
                </div>
            </div>
            <div class="md-foot">
                <button type="button" @click="addOpen=false" class="btn btn-s">Cancel</button>
                <button type="submit" class="btn btn-p">Create User</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection