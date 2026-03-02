<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OIMS — Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --teal:#0d9488;--teal2:#14b8a6;--navy:#0f172a;
            --muted:#94a3b8;--border:rgba(255,255,255,.08);--glass:rgba(255,255,255,.04);
        }
        body{font-family:'Inter',sans-serif;min-height:100vh;background:var(--navy);display:flex;overflow:hidden}

        /* ── LEFT PANEL ── */
        .left{flex:1;position:relative;display:flex;flex-direction:column;
            justify-content:center;padding:70px;overflow:hidden}
        .left::before{content:'';position:absolute;inset:0;
            background:radial-gradient(ellipse 80% 60% at 15% 50%,rgba(13,148,136,.32),transparent 65%),
                        radial-gradient(ellipse 50% 70% at 80% 20%,rgba(6,182,212,.13),transparent 60%)}
        .grid-bg{position:absolute;inset:0;
            background-image:linear-gradient(rgba(20,184,166,.06) 1px,transparent 1px),
                             linear-gradient(90deg,rgba(20,184,166,.06) 1px,transparent 1px);
            background-size:52px 52px;animation:drift 25s linear infinite}
        @keyframes drift{to{background-position:52px 52px}}
        .blob{position:absolute;border-radius:50%;filter:blur(70px);opacity:.2;animation:bob 9s ease-in-out infinite}
        .b1{width:350px;height:350px;background:#0d9488;top:-100px;left:-100px}
        .b2{width:260px;height:260px;background:#0891b2;bottom:60px;right:40px;animation-delay:-3.5s}
        .b3{width:200px;height:200px;background:#7c3aed;top:45%;left:42%;animation-delay:-6s}
        @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-28px)}}
        .lc{position:relative;z-index:2}
        .brand{display:inline-flex;align-items:center;gap:14px;margin-bottom:60px}
        .brand-ico{width:50px;height:50px;background:linear-gradient(135deg,var(--teal),#0891b2);
            border-radius:14px;display:grid;place-items:center;font-size:24px;
            box-shadow:0 8px 32px rgba(13,148,136,.45)}
        .brand-txt{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#fff}
        .brand-sub{font-size:11px;color:var(--muted);margin-top:1px}
        h1{font-family:'Syne',sans-serif;font-size:clamp(2.2rem,4vw,3.5rem);font-weight:800;
            color:#fff;line-height:1.1;letter-spacing:-1.5px;margin-bottom:18px}
        h1 em{font-style:normal;background:linear-gradient(135deg,var(--teal2),#38bdf8);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .sub{font-size:1rem;color:var(--muted);line-height:1.7;max-width:390px;margin-bottom:52px}
        .pills{display:flex;gap:32px}
        .pill .num{font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:var(--teal2)}
        .pill .lbl{font-size:.75rem;color:var(--muted);margin-top:2px}

        /* ── RIGHT PANEL ── */
        .right{width:460px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
            padding:44px;border-left:1px solid var(--border);
            background:rgba(15,23,42,.65);backdrop-filter:blur(24px)}
        .box{width:100%;max-width:375px}
        .box h2{font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:800;
            color:#fff;letter-spacing:-.4px;margin-bottom:6px}
        .box .desc{font-size:.875rem;color:var(--muted);margin-bottom:36px}
        .err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);
            border-radius:10px;padding:12px 16px;margin-bottom:22px;color:#fca5a5;font-size:.85rem}
        .fg{margin-bottom:18px}
        .fg label{display:block;font-size:.72rem;font-weight:600;color:var(--muted);
            letter-spacing:.07em;text-transform:uppercase;margin-bottom:7px}
        .fg input{width:100%;background:var(--glass);border:1px solid var(--border);
            border-radius:10px;padding:13px 15px;color:#fff;font-family:'Inter',sans-serif;
            font-size:.9rem;outline:none;transition:border-color .2s,box-shadow .2s}
        .fg input:focus{border-color:var(--teal);background:rgba(13,148,136,.09);
            box-shadow:0 0 0 3px rgba(13,148,136,.18)}
        .fg input::placeholder{color:#475569}
        .rem-row{display:flex;align-items:center;gap:8px;margin-bottom:26px}
        .rem-row input{width:15px;height:15px;accent-color:var(--teal)}
        .rem-row span{font-size:.82rem;color:var(--muted)}
        .btn-go{width:100%;background:linear-gradient(135deg,var(--teal),#0891b2);color:#fff;
            border:none;border-radius:10px;padding:14px;font-family:'Syne',sans-serif;
            font-size:1rem;font-weight:700;cursor:pointer;
            box-shadow:0 8px 28px rgba(13,148,136,.38);transition:transform .15s,box-shadow .2s}
        .btn-go:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(13,148,136,.5)}
        .divider{display:flex;align-items:center;gap:12px;margin:28px 0}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
        .divider span{font-size:.72rem;color:#334155}
        .quick{display:flex;flex-direction:column;gap:8px}
        .qb{display:flex;align-items:center;justify-content:space-between;
            background:var(--glass);border:1px solid var(--border);border-radius:9px;
            padding:11px 14px;cursor:pointer;transition:background .15s,border-color .15s}
        .qb:hover{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15)}
        .ql{display:flex;align-items:center;gap:10px}
        .dot{width:8px;height:8px;border-radius:50%}
        .da{background:#10b981}.ds{background:#f59e0b}.df{background:#3b82f6}
        .qname{font-size:.83rem;color:#e2e8f0;font-weight:600}
        .qrole{font-size:.68rem;color:var(--muted)}
        .qemail{font-size:.7rem;color:#475569;font-family:monospace}
    </style>
</head>
<body>

<!-- LEFT -->
<div class="left">
    <div class="grid-bg"></div>
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
    <div class="lc">
        <div class="brand">
            <div class="brand-ico">📦</div>
            <div>
                <div class="brand-txt">OIMS</div>
                <div class="brand-sub">Ketea Company</div>
            </div>
        </div>
        <h1>Smart Inventory<br><em>Management</em><br>System</h1>
        <p class="sub">Real-time asset tracking, consumable stock alerts, and full issuance history — all in one digital platform for Ketea Company.</p>
        <div class="pills">
            <div class="pill"><div class="num">587</div><div class="lbl">Employees</div></div>
            <div class="pill"><div class="num">3</div><div class="lbl">Access Levels</div></div>
            <div class="pill"><div class="num">100%</div><div class="lbl">Digital Records</div></div>
        </div>
    </div>
</div>

<!-- RIGHT -->
<div class="right">
    <div class="box">
        <h2>Welcome back</h2>
        <p class="desc">Sign in to your OIMS account to continue</p>

        @if ($errors->any())
            <div class="err">⚠️ {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="fg">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="you@ketea.com" required autofocus>
            </div>
            <div class="fg">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="rem-row">
                <input type="checkbox" name="remember" id="rem">
                <span>Remember me</span>
            </div>
            <button type="submit" class="btn-go">Sign In →</button>
        </form>

        <div class="divider"><span>Quick demo access — click to fill</span></div>

        <div class="quick">
            <div class="qb" onclick="fill('admin@ketea.com')">
                <div class="ql">
                    <div class="dot da"></div>
                    <div><div class="qname">Admin User</div><div class="qrole">Full Access</div></div>
                </div>
                <div class="qemail">admin@ketea.com</div>
            </div>
~
        </div>
    </div>
</div>

<script>
function fill(email) {
    document.querySelector('input[name=email]').value = email;
    document.querySelector('input[name=password]').value = 'password';
}
</script>
</body>
</html>