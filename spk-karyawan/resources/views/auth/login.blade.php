<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — SPK Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{box-sizing:border-box}
body{background:#0f172a;display:flex;align-items:center;justify-content:center;min-height:100vh;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
.login-wrap{width:100%;max-width:380px;padding:20px}
.logo-box{width:48px;height:48px;background:#2563eb;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.logo-box i{color:#fff;font-size:24px}
.brand-title{text-align:center;color:#fff;font-size:16px;font-weight:600;margin-bottom:4px}
.brand-sub{text-align:center;color:#475569;font-size:12px;margin-bottom:24px}
.card-login{background:#1e293b;border:0.5px solid rgba(255,255,255,.08);border-radius:12px;padding:24px}
.form-label{font-size:11px;font-weight:600;color:#94a3b8;margin-bottom:5px;display:block}
.form-control{width:100%;background:#0f172a;border:0.5px solid rgba(255,255,255,.12);border-radius:7px;padding:8px 12px;color:#e2e8f0;font-size:12px;outline:none;transition:border-color .15s}
.form-control:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.2)}
.form-control::placeholder{color:#475569}
.btn-login{width:100%;background:#2563eb;border:none;color:#fff;padding:9px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s;margin-top:4px}
.btn-login:hover{background:#1d4ed8}
.error-box{background:#FCEBEB;border:0.5px solid #fca5a5;border-radius:7px;padding:8px 12px;font-size:11px;color:#791F1F;margin-bottom:14px;display:flex;align-items:center;gap:6px}
.form-group{margin-bottom:14px}
</style>
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <div class="logo-box"><i class="ti ti-bolt"></i></div>
    <div class="brand-title">SPK Karyawan Terbaik</div>
    <div class="brand-sub">PT Cempaka Indah Abadi</div>

    <div class="card-login">
        @if($errors->any())
        <div class="error-box">
            <i class="ti ti-alert-circle" style="font-size:14px;flex-shrink:0"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                    value="{{ old('username') }}" placeholder="Masukkan username" autofocus required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>