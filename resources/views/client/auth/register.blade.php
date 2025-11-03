{{-- resources/views/client/auth/register.blade.php --}}
@extends('layouts.guest')
@section('content')
    <div class="max-w-md mx-auto p-6 glass rounded-2xl mt-10">
        <h1 class="text-2xl font-bold mb-4">Create Client Account</h1>

        @if($errors->any())
            <div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('client.register') }}" class="space-y-4">
            @csrf
            <input name="first_name" class="w-full input-field rounded-xl p-3" placeholder="First name"
                   value="{{ old('first_name') }}" required>
            <input name="last_name" class="w-full input-field rounded-xl p-3" placeholder="Last name"
                   value="{{ old('last_name') }}">
            <input name="email" type="email" class="w-full input-field rounded-xl p-3" placeholder="Email"
                   value="{{ old('email') }}" required>
            <input name="password" type="password" class="w-full input-field rounded-xl p-3" placeholder="Password"
                   required>
            <input name="password_confirmation" type="password" class="w-full input-field rounded-xl p-3"
                   placeholder="Confirm password" required>
            <button class="btn-primary w-full h-11 rounded-xl text-white font-semibold">Create account</button>
        </form>

        <div class="mt-4 text-sm text-gray-400">
            Already have an account?
            <a class="underline" href="{{ route('client.login') }}">Log in</a>
        </div>
    </div>
@endsection
