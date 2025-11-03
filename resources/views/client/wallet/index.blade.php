@extends('layouts.guest')
@section('title','Wallet')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Wallet</h1>
        <div class="rounded-full px-3 py-1 glass text-sm">Balance: <span
                    class="font-semibold">{{ auth('client')->user()->coins }}</span> coins
        </div>
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-white/5">
            <tr>
                <th class="text-left p-3">Date</th>
                <th class="text-left p-3">Type</th>
                <th class="text-right p-3">Amount</th>
                <th class="text-right p-3">Balance</th>
            </tr>
            </thead>
            <tbody>
            @forelse($tx as $t)
                <tr class="border-t border-white/10">
                    <td class="p-3">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                    <td class="p-3">{{ str_replace('_',' ', $t->type) }}</td>
                    <td class="p-3 text-right {{ $t->amount >= 0 ? 'text-green-300' : 'text-red-300' }}">
                        {{ $t->amount >= 0 ? '+' : '' }}{{ $t->amount }}
                    </td>
                    <td class="p-3 text-right">{{ $t->balance_after }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-400">No transactions yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tx->links() }}</div>
@endsection
