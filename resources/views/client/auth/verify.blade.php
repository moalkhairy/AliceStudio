@extends('layouts.guest')
@section('title','Verify your email')

@section('content')
    <div class="max-w-md mx-auto glass rounded-2xl p-6">
        <h1 class="text-2xl font-bold mb-2">Verify your email</h1>
        <p class="text-gray-400 text-sm mb-4">Enter the 6-digit code we sent to {{ auth('client')->user()->email }}.</p>

        @if(session('status'))
            <div class="mb-3 rounded-xl p-3 glass text-sm">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-3 text-sm text-red-300">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('client.verify.perform') }}" class="space-y-4">
            @csrf
            <input name="code" class="w-full input-field rounded-xl p-3" placeholder="Enter code (e.g. 123456)"
                   maxlength="6" required>
            <button class="btn-primary w-full h-11 rounded-xl text-white font-semibold">Verify</button>
        </form>

        <form method="POST" action="{{ route('client.verify.send') }}" class="mt-3">
            @csrf
            <button class="btn-secondary w-full h-11 rounded-xl">Resend code</button>
        </form>
    </div>
@endsection
