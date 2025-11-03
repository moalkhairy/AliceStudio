{{-- resources/views/client/auth/login.blade.php --}}
@extends('layouts.guest') {{-- or your main layout --}}
@section('content')
    <div class="max-w-md mx-auto p-6 glass rounded-2xl mt-10">
        <h1 class="text-2xl font-bold mb-4">Client Login</h1>

        @if($errors->any())
            <div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('client.login') }}" class="space-y-4">
            @csrf
            <input name="email" type="email" class="w-full input-field rounded-xl p-3" placeholder="Email"
                   value="{{ old('email') }}" required>
            <input name="password" type="password" class="w-full input-field rounded-xl p-3" placeholder="Password"
                   required>
            <label class="flex items-center gap-2 text-sm text-gray-300">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button class="btn-primary w-full h-11 rounded-xl text-white font-semibold">Log in</button>
        </form>

        <div class="mt-4 text-sm text-gray-400 flex items-center justify-between">
            <a href="{{ route('client.password.request') }}">Forgot password?</a>
            <a href="{{ route('client.register') }}">Create account</a>
        </div>

        <div class="mt-6">
            <a href="{{ route('client.oauth.redirect','google') }}"
               class="btn-secondary w-full inline-flex items-center justify-center h-11 rounded-xl">Continue with
                Google</a>
            {{-- <a href="{{ route('client.oauth.redirect','facebook') }}" class="btn-secondary w-full inline-flex items-center justify-center h-11 rounded-xl mt-2">Continue with Facebook</a> --}}
        </div>
    </div>
@endsection
