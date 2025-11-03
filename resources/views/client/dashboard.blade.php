@extends('layouts.guest')
@section('title','Client Dashboard')

@section('content')
    <div class="glass rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Welcome, {{ auth('client')->user()->name }}</h1>
            <form action="{{ route('client.logout') }}" method="POST">
                @csrf
                <button class="btn-secondary px-4 py-2 rounded-lg text-sm">Logout</button>
            </form>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div class="glass rounded-xl p-4">
                <div class="text-sm text-gray-400 mb-1">Email</div>
                <div class="font-medium">{{ auth('client')->user()->email }}</div>
            </div>
            <div class="glass rounded-xl p-4">
                <div class="text-sm text-gray-400 mb-1">First name</div>
                <div class="font-medium">{{ auth('client')->user()->first_name }}</div>
            </div>
            <div class="glass rounded-xl p-4">
                <div class="text-sm text-gray-400 mb-1">Last name</div>
                <div class="font-medium">{{ auth('client')->user()->last_name }}</div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('client.profile') }}"
               class="btn-primary inline-flex items-center h-11 px-6 rounded-xl text-white font-semibold">
                Edit Profile
            </a>
        </div>
    </div>
@endsection
