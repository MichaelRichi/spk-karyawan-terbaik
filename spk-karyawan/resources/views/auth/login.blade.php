<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — SPK Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f4f6f9;display:flex;align-items:center;justify-content:center;min-height:100vh}</style>
</head>
<body>
<div style="width:100%;max-width:380px">
    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1">SPK Karyawan Terbaik</h5>
        <small class="text-muted">PT Cempaka Indah Abadi</small>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                        value="{{ old('username') }}" placeholder="Masukkan username" autofocus required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>