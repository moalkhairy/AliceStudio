@extends('layouts.guest')
@section('title','Client Dashboard')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-4 rounded-xl p-3 bg-emerald-500/10 border border-emerald-400/40 text-sm text-white">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl p-3 bg-red-500/10 border border-red-400/40 text-sm text-white">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="glass rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h1 class="text-2xl font-bold">
                        Welcome, {{ auth('client')->user()->first_name ?? auth('client')->user()->name }}</h1>
                    <p class="text-sm text-white/60">Here’s a quick snapshot of your account.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('client.packages.index') }}"
                       class="btn-primary inline-flex items-center h-10 px-4 rounded-lg text-white font-semibold">Buy
                        Coins</a>

                    <a href="{{ url('/') }}"
                       class="btn-secondary inline-flex items-center h-10 px-4 rounded-lg font-semibold">Creator</a>

                    <form action="{{ route('client.logout') }}" method="POST">
                        @csrf
                        <button class="btn-secondary h-10 px-4 rounded-lg text-sm">Logout</button>
                    </form>
                </div>
            </div>

            {{-- Email / Name row (original blocks) --}}
            <div class="grid md:grid-cols-3 gap-4 mt-4">
                <div class="glass rounded-xl p-4">
                    <div class="text-sm text-gray-400 mb-1">Email</div>
                    <div class="font-medium">{{ auth('client')->user()->email }}</div>
                    <div class="mt-2">
                        @if(auth('client')->user()->email_verified_at)
                            <span class="px-2 py-1 rounded-md bg-emerald-500/15 border border-emerald-400/30 text-emerald-300 text-xs">Verified</span>
                        @else
                            <span class="px-2 py-1 rounded-md bg-yellow-500/15 border border-yellow-400/30 text-yellow-300 text-xs">Not verified</span>
                        @endif
                    </div>
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

            {{-- Quick stat cards --}}
            @php
                $client = auth('client')->user();
                $ordersTotal = isset($orders) && $orders instanceof \Illuminate\Contracts\Pagination\Paginator
                    ? $orders->total()
                    : (method_exists($client, 'orders') ? $client->orders()->count() : 0);
                $lastOrder = isset($orders) && count($orders) ? $orders->first() : (method_exists($client, 'orders') ? $client->orders()->latest()->with('package')->first() : null);
            @endphp

            <div class="grid md:grid-cols-4 gap-4 mt-4">
                <div class="glass rounded-xl p-4">
                    <div class="text-sm text-gray-400">Coins</div>
                    <div class="text-3xl font-bold mt-1">{{ (int)($client->coins ?? 0) }}</div>
                    <div class="mt-3 text-right">
                        <a class="text-xs underline text-white/70 hover:text-white"
                           href="{{ route('client.packages.index') }}">Top up →</a>
                    </div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="text-sm text-gray-400">Total Orders</div>
                    <div class="text-3xl font-bold mt-1">{{ $ordersTotal }}</div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="text-sm text-gray-400">Last Package</div>
                    <div class="mt-1 text-sm">
                        @if($lastOrder?->package)
                            <div class="font-semibold">{{ $lastOrder->package->title }}</div>
                            <div class="text-white/60">{{ number_format($lastOrder->package->coins) }} coins</div>
                        @else
                            <span class="text-white/60">—</span>
                        @endif
                    </div>
                </div>

                <div class="glass rounded-xl p-4">
                    <div class="text-sm text-gray-400">Profile</div>
                    <div class="mt-2">
                        <a href="{{ route('client.profile') }}"
                           class="btn-primary inline-flex items-center h-10 px-4 rounded-lg text-white font-semibold">Edit
                            Profile</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- My Orders --}}
        <div class="glass rounded-2xl p-6 mt-6">
            <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                <h2 class="text-lg font-semibold">My Orders</h2>

                {{-- Filters --}}
                <form method="get" class="flex items-end gap-2 text-sm">
                    <div>
                        <label class="block text-white/60 text-xs mb-1">Status</label>
                        <select name="status" class="rounded-lg bg-white/5 border border-white/10 px-2 py-1.5">
                            <option value="">All</option>
                            @foreach(['paid','pending','failed'] as $s)
                                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-white/60 text-xs mb-1">From</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                               class="rounded-lg bg-white/5 border border-white/10 px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-white/60 text-xs mb-1">To</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                               class="rounded-lg bg-white/5 border border-white/10 px-2 py-1.5">
                    </div>
                    <button class="rounded-lg px-3 py-2 btn-secondary">Filter</button>
                </form>
            </div>

            @isset($orders)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-white/60">
                        <tr class="text-left">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Package</th>
                            <th class="px-4 py-2">Coins</th>
                            <th class="px-4 py-2">Final</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $o)
                            <tr class="border-t border-white/5">
                                <td class="px-4 py-2">{{ $o->id }}</td>
                                <td class="px-4 py-2">{{ $o->package?->title ?? '—' }}</td>
                                <td class="px-4 py-2">{{ number_format($o->package?->coins ?? 0) }}</td>
                                <td class="px-4 py-2">{{ number_format($o->final_price,2) }} {{ $o->currency }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $map = [
                                            'paid'    => 'bg-emerald-500/15 text-emerald-300 border-emerald-400/30',
                                            'pending' => 'bg-yellow-500/15 text-yellow-300 border-yellow-400/30',
                                            'failed'  => 'bg-red-500/15 text-red-300 border-red-400/30'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-md border {{ $map[$o->status] ?? 'bg-white/10 text-white/70 border-white/20' }}">
                                    {{ ucfirst($o->status) }}
                                </span>
                                </td>
                                <td class="px-4 py-2 text-white/60">{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-white/60">No orders yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->onEachSide(1)->links() }}
                </div>
            @else
                <div class="text-white/70 text-sm">
                    No orders data bound. Make sure your controller passes a paginated <code>$orders</code>
                    (see <em>DashboardController@index</em> we discussed).
                </div>
            @endisset
        </div>
    </div>
@endsection
