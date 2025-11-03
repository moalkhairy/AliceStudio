@extends('layouts.guest')
@section('title','Buy Coins')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Buy Coins</h1>
        <div class="rounded-full px-3 py-1 glass text-sm">Balance: <span
                    class="font-semibold">{{ auth('client')->user()->coins }}</span> coins
        </div>
    </div>

    @if($packages->isEmpty())
        <div class="glass rounded-2xl p-6 text-gray-400">No packages available yet.</div>
    @else
        <div class="grid md:grid-cols-3 gap-4">
            @foreach($packages as $p)
                <div class="glass rounded-2xl p-5">
                    <div class="text-lg font-semibold mb-1">{{ $p->title }}</div>
                    <div class="text-sm text-gray-400 mb-3">{{ $p->coins }} coins</div>
                    <div class="flex items-end gap-2 mb-4">
                        @if($p->discount_percent)
                            <span class="text-sm line-through text-gray-500">{{ number_format($p->price,2) }} {{ $p->currency }}</span>
                            <span class="text-sm bg-green-500/20 border border-green-500/40 rounded px-2 py-0.5">{{ $p->discount_percent }}% off</span>
                        @endif
                    </div>
                    <div class="text-xl font-bold mb-4">{{ number_format($p->final_price,2) }} {{ $p->currency }}</div>

                    <form action="{{ route('client.packages.buy',$p) }}" method="POST">
                        @csrf
                        <button class="btn-primary w-full h-11 rounded-xl text-white font-semibold">Buy now</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection
