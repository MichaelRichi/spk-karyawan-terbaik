<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — PT Cempaka Indah Abadi</title>
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;display:flex;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f0f4f8}
.left{flex:1;background:linear-gradient(145deg,#1a3a6b 0%,#1e4fa0 50%,#1565C0 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;position:relative;overflow:hidden}
.left::before{content:'';position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.05)}
.left::after{content:'';position:absolute;bottom:-60px;left:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.04)}
.logo-circle{width:130px;height:130px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(0,0,0,.25);margin-bottom:28px;flex-shrink:0;z-index:1}
.logo-circle img{width:90px;height:90px;object-fit:contain}
.brand-name{color:#fff;font-size:26px;font-weight:800;text-align:center;z-index:1;line-height:1.2;margin-bottom:6px}
.brand-city{color:rgba(255,255,255,.7);font-size:14px;text-align:center;z-index:1;margin-bottom:20px}
.brand-tag{display:flex;gap:8px;z-index:1;flex-wrap:wrap;justify-content:center}
.tag{background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);font-size:11px;padding:4px 10px;border-radius:20px;border:0.5px solid rgba(255,255,255,.25)}
.right{width:440px;background:#fff;display:flex;align-items:center;justify-content:center;padding:48px 40px;box-shadow:-8px 0 40px rgba(0,0,0,.08)}
.form-wrap{width:100%}
.form-title{font-size:24px;font-weight:800;color:#1e293b;margin-bottom:6px}
.form-sub{font-size:13px;color:#64748b;margin-bottom:32px}
.form-label{font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;display:block}
.input-wrap{position:relative;margin-bottom:18px}
.input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px}
.form-control{width:100%;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 12px 10px 38px;color:#1e293b;font-size:13px;outline:none;transition:all .15s}
.form-control:focus{border-color:#1e4fa0;box-shadow:0 0 0 3px rgba(30,79,160,.1);background:#fff}
.form-control::placeholder{color:#94a3b8}
.btn-login{width:100%;background:linear-gradient(135deg,#1e4fa0,#1565C0);border:none;color:#fff;padding:12px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:all .15s;margin-top:4px;letter-spacing:.3px}
.btn-login:hover{background:linear-gradient(135deg,#1a3a6b,#1e4fa0);transform:translateY(-1px);box-shadow:0 4px 16px rgba(30,79,160,.35)}
.error-box{background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;font-size:12px;color:#991b1b;margin-bottom:20px;display:flex;align-items:center;gap:8px}
@media(max-width:700px){
    body{flex-direction:column}
    .left{padding:32px 24px;min-height:220px}
    .right{width:100%;padding:32px 24px}
    .logo-circle{width:90px;height:90px}
    .logo-circle img{width:65px;height:65px}
    .brand-name{font-size:20px}
}
</style>
</head>
<body>

<div class="left">
    <div class="logo-circle">
        <img src="{{ asset('images/logo-cia.png') }}" alt="Logo PT CIA">
    </div>
    <div class="brand-name">PT Cempaka Indah Abadi</div>
    <div class="brand-city">Palembang, Sumatera Selatan</div>
    <div class="brand-tag">
        <span class="tag">Instalatir PLN</span>
        <span class="tag">Supplier</span>
        <span class="tag">Contractor</span>
    </div>
</div>

<div class="right">
    <div class="form-wrap">
        <div class="form-title">Selamat Datang</div>
        <div class="form-sub">Masuk ke Sistem Penilaian Karyawan</div>

        @if($errors->any())
        <div class="error-box">
            <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div>
                <label class="form-label">Username</label>
                <div class="input-wrap">
                    <i class="ti ti-user input-icon"></i>
                    <input type="text" name="username" class="form-control"
                        value="{{ old('username') }}" placeholder="Masukkan username" autofocus required>
                </div>
            </div>
            <div>
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="ti ti-lock input-icon"></i>
                    <input type="password" name="password" id="inp-pwd" class="form-control"
                        placeholder="Masukkan password" required>
                    <button type="button" onclick="togglePwd()"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8">
                        <i class="ti ti-eye" id="eye-icon" style="font-size:16px"></i>
                    </button>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;margin-top:-6px">
                <input type="checkbox" name="remember" id="remember" value="1"
                    style="width:15px;height:15px;accent-color:#1e4fa0;cursor:pointer">
                <label for="remember" style="font-size:12px;color:#64748b;cursor:pointer;user-select:none">
                    Ingat saya
                </label>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
            <div style="text-align:center;margin-top:14px;font-size:11px;color:#94a3b8">
                Lupa password? Hubungi <strong style="color:#1e4fa0">Admin</strong>.
            </div>
        </form>
    </div>
</div>

<script>
function togglePwd() {
    const inp = document.getElementById('inp-pwd');
    const eye = document.getElementById('eye-icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.className = 'ti ti-eye-off';
    } else {
        inp.type = 'password';
        eye.className = 'ti ti-eye';
    }
}
</script>
</body>
</html>