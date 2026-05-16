<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-5">
        <div class="mb-3">
            <img src="/villa-icon.png" alt="Villa Cilame Icon" style="max-width: 120px; height: auto; object-fit: contain;">
        </div>
        <h2 class="h5 font-weight-bold text-dark mb-0">Pengelolaan Kompleks Villa Cilame Indah</h2>
    </div>

    <form method="POST" action="{{ route('login') }}" class="user">
        @csrf

        <!-- Username -->
        <div class="form-group mb-3">
            <input id="email" type="email" name="email" :value="old('email')" class="form-control form-control-lg" placeholder="Username" required autofocus autocomplete="username" style="border-radius: 25px; border: 1px solid #e3e6f0;">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
        </div>

        <!-- Password -->
        <div class="form-group mb-3">
            <input id="password" type="password" name="password" class="form-control form-control-lg" placeholder="Password" required autocomplete="current-password" style="border-radius: 25px; border: 1px solid #e3e6f0;">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
        </div>

        <!-- Remember Me -->
        <div class="form-group mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label class="form-check-label small" for="remember_me">
                    {{ __('Ingat saya') }}
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100" style="border-radius: 25px; font-weight: 600;">
            LOGIN
        </button>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="small text-decoration-none" href="{{ route('password.request') }}">{{ __('Lupa password?') }}</a>
            </div>
        @endif

        @if (Route::has('register'))
            <div class="text-center mt-3">
                <span class="small">Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none">Daftar</a></span>
            </div>
        @endif
    </form>

    <hr class="my-4">
    <div class="text-center">
        <p class="small text-muted mb-0">Copyright © 2026 - <span class="text-danger">Warga VCI</span></p>
    </div>
</x-guest-layout>
