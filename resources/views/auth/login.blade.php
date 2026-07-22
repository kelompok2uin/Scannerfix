<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BPKA Scanner</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="login-overlay active">
    <div class="login-background-glow"></div>

    <div class="login-card">
        <div style="text-align:center; margin-bottom:2rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="logo-icon-large" style="margin:0 auto 0.75rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
            <h1 class="login-title">BPKA <span>Scanner</span></h1>
            <p style="color:var(--text-secondary); font-size:0.85rem; margin-top:0.5rem;">Masuk ke sistem manajemen dokumen</p>
        </div>

        @if ($errors->any())
            <div class="login-error-msg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="input-icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input type="email" id="email" name="email" class="form-control padded-left" value="{{ old('email') }}" placeholder="admin@bpka.go.id" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" class="input-icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" id="password" name="password" class="form-control padded-left" placeholder="Masukkan password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:0.5rem;">
                <input type="checkbox" id="remember" name="remember" style="accent-color:var(--accent-color);">
                <label for="remember" style="margin:0; text-transform:none; letter-spacing:0; font-size:0.85rem;">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary btn-glow w-full" style="justify-content:center; padding:0.75rem;">
                Masuk
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
