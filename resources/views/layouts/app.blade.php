<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OIMS — @yield('title','Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --teal:#0d9488;--teal2:#14b8a6;--teal-d:#0f766e;
            --navy:#0f172a;--slate:#1e293b;--muted:#94a3b8;
            --border:#e2e8f0;--sw:260px;--hh:64px;
        }
        html,body{height:100%}
        
        /* GRADIENT BACKGROUND */
        body{
            font-family:'Inter',sans-serif;
            color:var(--slate);
            display:flex;
            background:linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size:400% 400%;
            animation:gradientShift 20s ease infinite;
            position:relative;
        }
        body::before{
            content:'';
            position:fixed;
            inset:0;
            background:rgba(255,255,255,.92);
            z-index:0;
            pointer-events:none;
        }
        @keyframes gradientShift{
            0%,100%{background-position:0% 50%}
            50%{background-position:100% 50%}
        }

        /* ═══ SIDEBAR ═══ */
        .sidebar{width:var(--sw);flex-shrink:0;
            background:linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            height:100vh;position:fixed;left:0;top:0;
            display:flex;flex-direction:column;z-index:100;overflow:hidden;
            box-shadow:4px 0 24px rgba(0,0,0,.15)}
        .sidebar::before{content:'';position:absolute;top:-80px;left:-80px;width:240px;height:240px;
            background:radial-gradient(circle,rgba(13,148,136,.35),transparent 70%);pointer-events:none}

        .sb-brand{padding:22px 18px 18px;border-bottom:1px solid rgba(255,255,255,.1);position:relative;z-index:1}
        .sb-brand-row{display:flex;align-items:center;gap:12px;margin-bottom:3px}
        .sb-ico{width:38px;height:38px;background:linear-gradient(135deg,var(--teal),#0891b2);
            border-radius:10px;display:grid;place-items:center;font-size:18px;flex-shrink:0;
            box-shadow:0 4px 16px rgba(13,148,136,.5)}
        .sb-name{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:#fff;
            text-shadow:0 2px 4px rgba(0,0,0,.2)}
        .sb-company{font-size:.7rem;color:rgba(255,255,255,.65);padding-left:50px}

        .sb-nav{flex:1;padding:14px 10px;overflow-y:auto}
        .sb-section{margin-bottom:22px}
        .sb-label{font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
            color:#64748b;padding:0 8px;margin-bottom:5px}
        .sb-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;
            color:#94a3b8;text-decoration:none;font-size:.85rem;font-weight:500;
            transition:all .15s;margin-bottom:2px;position:relative}
        .sb-link:hover{background:rgba(255,255,255,.08);color:#e2e8f0}
        .sb-link.on{background:rgba(13,148,136,.22);color:#5eead4;box-shadow:0 0 12px rgba(13,148,136,.3)}
        .sb-link.on::before{content:'';position:absolute;left:0;top:6px;bottom:6px;
            width:3px;background:#5eead4;border-radius:0 3px 3px 0;box-shadow:0 0 8px #5eead4}
        .sb-badge{margin-left:auto;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;
            font-size:.62rem;font-weight:700;padding:2px 7px;border-radius:20px;
            box-shadow:0 2px 8px rgba(239,68,68,.4);animation:pulse 2s infinite}
        @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}

        .sb-user{padding:14px;border-top:1px solid rgba(255,255,255,.1);
            display:flex;align-items:center;gap:10px;
            background:linear-gradient(135deg,rgba(13,148,136,.1),rgba(6,182,212,.1))}
        .sb-av{width:34px;height:34px;border-radius:50%;
            background:linear-gradient(135deg,var(--teal),#0891b2);
            display:grid;place-items:center;font-family:'Syne',sans-serif;
            font-size:.85rem;font-weight:700;color:#fff;flex-shrink:0;
            box-shadow:0 2px 8px rgba(13,148,136,.4)}
        .sb-uname{font-size:.82rem;font-weight:600;color:#e2e8f0;
            white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .sb-urole{font-size:.68rem;color:rgba(255,255,255,.6);text-transform:capitalize}
        .sb-logout{background:rgba(239,68,68,.15);border:none;cursor:pointer;
            color:#fca5a5;font-size:15px;padding:4px 8px;border-radius:6px;
            transition:all .15s;margin-left:auto;display:grid;place-items:center}
        .sb-logout:hover{background:#ef4444;color:#fff;transform:scale(1.1)}

        /* ═══ MAIN ═══ */
        .wrap{margin-left:var(--sw);flex:1;min-height:100vh;display:flex;flex-direction:column;position:relative;z-index:1}

        .hdr{height:var(--hh);
            background:rgba(255,255,255,.95);
            backdrop-filter:blur(12px);
            border-bottom:1px solid rgba(226,232,240,.5);
            display:flex;align-items:center;justify-content:space-between;
            padding:0 30px;position:sticky;top:0;z-index:50;
            box-shadow:0 4px 12px rgba(0,0,0,.05)}
        .hdr-title{font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:700;
            background:linear-gradient(135deg,#667eea,#764ba2);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .hdr-r{display:flex;align-items:center;gap:12px}
        .alert-pill{display:flex;align-items:center;gap:6px;
            background:linear-gradient(135deg,#fef2f2,#fee2e2);
            border:1px solid #fecaca;color:#dc2626;font-size:.75rem;font-weight:600;
            padding:4px 11px;border-radius:20px;box-shadow:0 2px 8px rgba(239,68,68,.2)}
        .hdr-date{font-size:.78rem;color:var(--muted)}

        .content{flex:1;padding:28px 30px}

        /* flash */
        .flash{display:flex;align-items:center;gap:10px;border-radius:10px;
            padding:12px 18px;margin-bottom:22px;font-size:.875rem;font-weight:500;
            animation:slideIn .3s ease;box-shadow:0 4px 12px rgba(0,0,0,.08)}
        @keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        .flash-ok{background:linear-gradient(135deg,#f0fdf4,#dcfce7);
            border:1px solid #86efac;border-left:4px solid #22c55e;color:#15803d}
        .flash-err{background:linear-gradient(135deg,#fef2f2,#fee2e2);
            border:1px solid #fca5a5;border-left:4px solid #ef4444;color:#b91c1c}

        /* validation errors */
        .error-box{background:linear-gradient(135deg,#fef2f2,#fee2e2);
            border:2px solid #fca5a5;border-left:5px solid #ef4444;
            border-radius:10px;padding:16px 18px;margin-bottom:22px;
            box-shadow:0 4px 16px rgba(239,68,68,.2)}
        .error-box h4{font-size:.95rem;color:#dc2626;font-weight:700;margin-bottom:10px;
            display:flex;align-items:center;gap:8px}
        .error-box ul{margin-left:20px;color:#b91c1c}
        .error-box li{margin-bottom:6px;font-size:.85rem}

        /* ═══ COMPONENTS ═══ */

        /* Stat grid */
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:14px;margin-bottom:24px}
        .sc{background:rgba(255,255,255,.95);backdrop-filter:blur(10px);border-radius:14px;
            border:1px solid rgba(226,232,240,.8);padding:20px;position:relative;overflow:hidden;
            transition:transform .2s,box-shadow .2s}
        .sc:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.1)}
        .sc::after{content:'';position:absolute;top:0;right:0;width:80px;height:80px;
            border-radius:50%;background:var(--c);opacity:.08;transform:translate(20px,-20px)}
        .sc.blue{--c:#3b82f6}.sc.teal{--c:#0d9488}.sc.amber{--c:#f59e0b}
        .sc.red{--c:#ef4444}.sc.violet{--c:#8b5cf6}.sc.rose{--c:#f43f5e}
        .sc-ico{width:36px;height:36px;border-radius:9px;display:grid;place-items:center;
            font-size:17px;margin-bottom:14px;position:relative;z-index:1}
        .sc.blue  .sc-ico{background:#eff6ff}
        .sc.teal  .sc-ico{background:#f0fdfa}
        .sc.amber .sc-ico{background:#fffbeb}
        .sc.red   .sc-ico{background:#fef2f2}
        .sc.violet.sc-ico{background:#f5f3ff}
        .sc.rose  .sc-ico{background:#fff1f2}
        .sc-val{font-family:'Syne',sans-serif;font-size:1.9rem;font-weight:800;
            color:var(--navy);line-height:1;margin-bottom:3px;position:relative;z-index:1}
        .sc-lbl{font-size:.75rem;color:var(--muted);font-weight:500;position:relative;z-index:1}

        /* card */
        .card{background:rgba(255,255,255,.95);backdrop-filter:blur(10px);
            border-radius:14px;border:1px solid rgba(226,232,240,.8);overflow:hidden;
            box-shadow:0 4px 12px rgba(0,0,0,.06)}
        .card-hdr{padding:16px 22px;border-bottom:1px solid rgba(241,245,249,.8);
            display:flex;align-items:center;justify-content:space-between}
        .card-title{font-family:'Syne',sans-serif;font-size:.92rem;font-weight:700;color:var(--navy)}
        .card-body{padding:22px}

        /* buttons */
        .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 17px;border-radius:8px;
            font-family:'Inter',sans-serif;font-size:.845rem;font-weight:600;cursor:pointer;
            border:none;transition:all .15s;text-decoration:none;white-space:nowrap}
        .btn-p{background:linear-gradient(135deg,var(--teal),var(--teal-d));color:#fff;
            box-shadow:0 4px 14px rgba(13,148,136,.35)}
        .btn-p:hover{box-shadow:0 6px 20px rgba(13,148,136,.5);transform:translateY(-1px)}
        .btn-s{background:rgba(241,245,249,.95);color:#475569;border:1px solid rgba(226,232,240,.8)}
        .btn-s:hover{background:rgba(226,232,240,.95)}
        .btn-d{background:rgba(254,242,242,.95);color:#dc2626;border:1px solid #fecaca}
        .btn-d:hover{background:rgba(254,226,226,.95)}
        .btn-sm{padding:6px 12px;font-size:.78rem;border-radius:6px}

        /* table */
        .tbl-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        thead tr{background:rgba(248,250,252,.95);border-bottom:1px solid rgba(226,232,240,.8)}
        th{padding:11px 16px;text-align:left;font-size:.7rem;font-weight:700;
            letter-spacing:.06em;text-transform:uppercase;color:#64748b;white-space:nowrap}
        tbody tr{border-bottom:1px solid rgba(241,245,249,.6);transition:background .1s}
        tbody tr:hover{background:rgba(248,250,252,.6)}
        tbody tr:last-child{border-bottom:none}
        td{padding:13px 16px;font-size:.855rem;color:#374151}

        /* badges */
        .badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;
            border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap}
        .badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}
        .bg{background:#f0fdf4;color:#16a34a}
        .ba{background:#fffbeb;color:#d97706}
        .br{background:#fef2f2;color:#dc2626}
        .bb{background:#eff6ff;color:#2563eb}
        .bp{background:#f5f3ff;color:#7c3aed}
        .bs{background:#f8fafc;color:#475569}

        /* filter bar */
        .fbar{display:flex;gap:10px;align-items:center;margin-bottom:18px;flex-wrap:wrap}
        .sw{position:relative;flex:1;min-width:190px}
        .sw span{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px}
        .si{width:100%;padding:9px 12px 9px 34px;border:1px solid rgba(226,232,240,.8);border-radius:8px;
            font-family:'Inter',sans-serif;font-size:.845rem;background:rgba(255,255,255,.95);
            color:var(--slate);outline:none;transition:border-color .15s}
        .si:focus{border-color:var(--teal);box-shadow:0 0 0 3px rgba(13,148,136,.15)}
        .fsel{padding:9px 13px;border:1px solid rgba(226,232,240,.8);border-radius:8px;
            font-family:'Inter',sans-serif;font-size:.845rem;background:rgba(255,255,255,.95);
            color:#374151;outline:none;cursor:pointer}
        .fsel:focus{border-color:var(--teal)}

        /* modal */
        .mo{position:fixed;inset:0;background:rgba(15,23,42,.7);backdrop-filter:blur(8px);
            display:flex;align-items:center;justify-content:center;z-index:200;padding:20px}
        .md{background:rgba(255,255,255,.98);backdrop-filter:blur(10px);border-radius:16px;
            width:100%;max-width:500px;max-height:90vh;overflow-y:auto;
            box-shadow:0 24px 64px rgba(0,0,0,.3);animation:moin .2s ease}
        @keyframes moin{from{opacity:0;transform:scale(.94)}to{opacity:1;transform:scale(1)}}
        .md-hdr{padding:19px 22px;border-bottom:1px solid rgba(241,245,249,.8);
            display:flex;align-items:center;justify-content:space-between}
        .md-title{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--navy)}
        .md-x{width:27px;height:27px;border:none;background:rgba(241,245,249,.8);border-radius:6px;
            cursor:pointer;font-size:13px;display:grid;place-items:center;transition:background .15s}
        .md-x:hover{background:rgba(226,232,240,.95)}
        .md-body{padding:22px}
        .md-foot{padding:14px 22px;border-top:1px solid rgba(241,245,249,.8);
            display:flex;gap:10px;justify-content:flex-end}

        /* forms */
        .fg{margin-bottom:16px}
        .fl{display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:5px}
        .fl em{color:#ef4444;margin-left:2px;font-style:normal}
        .fi,.fse,.ft{width:100%;padding:9px 12px;border:1px solid rgba(226,232,240,.8);border-radius:8px;
            font-family:'Inter',sans-serif;font-size:.845rem;color:var(--slate);
            background:rgba(255,255,255,.95);outline:none;transition:border-color .15s,box-shadow .15s}
        .fi:focus,.fse:focus,.ft:focus{border-color:var(--teal);box-shadow:0 0 0 3px rgba(13,148,136,.15)}
        .fi.err,.fse.err,.ft.err{border-color:#ef4444;background:rgba(254,242,242,.5)}
        .ft{resize:vertical;min-height:76px}
        .f2{display:grid;grid-template-columns:1fr 1fr;gap:13px}
        .field-err{font-size:.72rem;color:#dc2626;margin-top:4px;font-weight:600}

        /* low stock bar */
        .low-bar{display:flex;align-items:center;gap:10px;
            background:linear-gradient(135deg,#fef2f2,#fee2e2);border:2px solid #fecaca;
            border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:.855rem;
            color:#b91c1c;box-shadow:0 4px 12px rgba(239,68,68,.2)}

        /* pagination */
        .pag{padding:13px 20px;border-top:1px solid rgba(241,245,249,.6)}
        .pag nav{display:flex;align-items:center;justify-content:flex-end;gap:4px}
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-row">
            <div class="sb-ico">📦</div>
            <div class="sb-name">OIMS</div>
        </div>
        <div class="sb-company">Ketea Company</div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">
            <div class="sb-label">Main</div>
            <a href="{{ route('dashboard') }}"
               class="sb-link {{ request()->routeIs('dashboard') ? 'on' : '' }}">
                🏠 <span>Dashboard</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-label">Inventory</div>

            {{-- CONSUMABLES VISIBLE TO ALL --}}
            <a href="{{ route('consumables.index') }}"
               class="sb-link {{ request()->routeIs('consumables.*') ? 'on' : '' }}">
                📦 <span>Consumables</span>
                @if(isset($lowStockBadge) && $lowStockBadge > 0)
                    <span class="sb-badge">{{ $lowStockBadge }}</span>
                @endif
            </a>

            @if(in_array(auth()->user()->role, ['admin','storekeeper']))
            <a href="{{ route('assets.index') }}"
               class="sb-link {{ request()->routeIs('assets.*') ? 'on' : '' }}">
                🖥️ <span>Assets</span>
            </a>
            <a href="{{ route('issuances.index') }}"
               class="sb-link {{ request()->routeIs('issuances.*') ? 'on' : '' }}">
                📋 <span>Issuances</span>
            </a>
            @endif
        </div>

        @if(auth()->user()->role === 'admin')
        <div class="sb-section">
            <div class="sb-label">Admin</div>
            <a href="{{ route('reports.index') }}"
               class="sb-link {{ request()->routeIs('reports.*') ? 'on' : '' }}">
                📊 <span>Reports</span>
            </a>
            <a href="{{ route('users.index') }}"
               class="sb-link {{ request()->routeIs('users.*') ? 'on' : '' }}">
                👥 <span>Users</span>
            </a>
        </div>
        @endif
    </nav>

    <div class="sb-user">
        <div class="sb-av">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
        <div style="flex:1;min-width:0">
            <div class="sb-uname">{{ auth()->user()->name }}</div>
            <div class="sb-urole">{{ auth()->user()->role }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout" title="Logout">⇥</button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div class="wrap">
    <header class="hdr">
        <div class="hdr-title">@yield('title','Dashboard')</div>
        <div class="hdr-r">
            @if(isset($lowStockBadge) && $lowStockBadge > 0)
                <div class="alert-pill">⚠️ {{ $lowStockBadge }} low stock</div>
            @endif
            <span class="hdr-date">{{ now()->format('D, d M Y') }}</span>
        </div>
    </header>

    <div class="content">
        {{-- Success Flash --}}
        @if(session('success'))
            <div class="flash flash-ok">✓ {{ session('success') }}</div>
        @endif

        {{-- Error Flash --}}
        @if(session('error'))
            <div class="flash flash-err">✕ {{ session('error') }}</div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="error-box">
                <h4>⚠️ Please fix the following errors:</h4>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>