@if(session('success'))
    <div class="mb-4 rounded-xl p-3 bg-emerald-500/10 border border-emerald-400/40 text-sm text-white/90">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 rounded-xl p-3 bg-red-500/10 border border-red-400/40 text-sm text-white/90">
        {{ session('error') }}
    </div>
@endif
