<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Moneyku') }} — Konfirmasi Password</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-orange-50 flex items-center justify-center">
        <div class="auth-full">
            <aside class="auth-left">
                <div>
                    <div style="display:flex; align-items:center; gap:0.8rem;">
                        <img src="{{ asset('images/moneyku-logo.svg') }}" alt="{{ config('app.name', 'Moneyku') }}" style="width:64px;height:64px;border-radius:12px; border:2px solid #fed7aa;background:#fff;">
                        <div>
                            <div style="font-weight:700; font-size:1.25rem; color:#7c2d12;">{{ config('app.name', 'Moneyku') }}</div>
                            <div style="font-size:0.9rem; color:#c2410c; margin-top:3px;">Kelola keuangan, lebih mudah</div>
                        </div>
                    </div>

                    <p style="margin-top:1.25rem; color:#7c2d12; line-height:1.5;">Ini area aman. Konfirmasi password untuk melanjutkan.</p>
                </div>

                <div style="display:flex; align-items:center; gap:0.75rem; margin-top:1.5rem;">
                    <img src="{{ asset('images/berkemah-team-logo.svg') }}" alt="Berkemah Team" style="width:40px;height:40px;border-radius:8px;border:1px solid #fed7aa;background:#fff;padding:4px;">
                    <div style="font-size:0.85rem;color:#c2410c;"><span style="display:block;font-weight:600;color:#ea580c;">Berkemah Team</span><span style="display:block;color:#fdba74;font-weight:500;">Powered by</span></div>
                </div>
            </aside>

            <div class="auth-right">
                <div class="auth-card">
                    <p style="font-weight:700; font-size:1.1rem; margin-bottom:1rem;">Konfirmasi Password</p>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div style="margin-bottom:0.9rem;">
                            <label for="password" style="display:block;font-size:0.85rem;font-weight:600;color:#6b7280;margin-bottom:0.35rem;">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full p-3 rounded-lg border" style="border-color:#e6edf7;">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <button type="submit" class="btn-primary" style="width:100%;">Konfirmasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
