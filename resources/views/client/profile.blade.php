@extends('layouts.guest')
@section('title','Client Profile')

@section('content')
    <div class="max-w-2xl mx-auto glass rounded-2xl p-6">
        <h1 class="text-2xl font-bold mb-4">Your Profile</h1>

        @if(session('status'))
            <div class="mb-3 rounded-xl p-3 glass text-sm">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>
        @endif

        {{-- Profile info --}}
        <form method="POST" action="{{ route('client.profile.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-300 mb-1">First name</label>
                <input name="first_name" class="w-full input-field rounded-xl p-3"
                       value="{{ old('first_name', auth('client')->user()->first_name) }}" required>
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1">Last name</label>
                <input name="last_name" class="w-full input-field rounded-xl p-3"
                       value="{{ old('last_name', auth('client')->user()->last_name) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1">Phone</label>
                <input name="phone" class="w-full input-field rounded-xl p-3"
                       value="{{ old('phone', auth('client')->user()->phone) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1">Email</label>
                <input name="email" type="email" class="w-full input-field rounded-xl p-3"
                       value="{{ old('email', auth('client')->user()->email) }}" required>
            </div>
            <button class="btn-primary h-11 px-6 rounded-xl text-white font-semibold">Save changes</button>
        </form>

        {{-- Password change --}}
        <div class="h-px bg-white/10 my-6"></div>
        <form method="POST" action="{{ route('client.profile.password') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-300 mb-1">Current password</label>
                <input name="current_password" type="password" class="w-full input-field rounded-xl p-3" required>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">New password</label>
                    <input name="password" type="password" class="w-full input-field rounded-xl p-3" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Confirm new password</label>
                    <input name="password_confirmation" type="password" class="w-full input-field rounded-xl p-3"
                           required>
                </div>
            </div>
            <button class="btn-secondary h-11 px-6 rounded-xl">Update password</button>
        </form>
    </div>
@endsection
