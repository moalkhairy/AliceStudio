<nav class="sticky top-0 z-40 backdrop-blur glass/50 border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-pink-500 inline-flex items-center justify-center font-black">A</span>
                <span class="font-semibold">Alice Studio</span>
            </a>
            <a href="{{ route('client.packages.index') }}" class="text-white/70 hover:text-white text-sm">Packages</a>
        </div>

        <div class="flex items-center gap-3">
            @auth('client')
                <span class="hidden sm:inline text-xs text-white/70">
                    Coins: <span class="font-semibold text-white">{{ auth('client')->user()->coins }}</span>
                </span>
                <a href="{{ route('client.dashboard') }}"
                   class="text-xs px-3 py-1.5 rounded-lg glass hover:bg-white/10 transition">
                    Profile
                </a>
                <form method="post" action="{{ route('client.logout') }}">
                    @csrf
                    <button class="text-xs px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('client.login') }}"
                   class="text-xs px-3 py-1.5 rounded-lg glass hover:bg-white/10 transition">Login</a>
                <a href="{{ route('client.register') }}"
                   class="text-xs px-3 py-1.5 rounded-lg btn-primary text-white transition">Register</a>
            @endauth
        </div>
    </div>
</nav>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    @include('client.partials.flashes')
    @auth('client')
        @if((auth('client')->user()->coins ?? 0) < 1)
            <div class="mb-4 rounded-xl p-3 bg-yellow-500/10 border border-yellow-400/40 text-sm text-white/90 flex items-center justify-between">
                <span>You have 0 coins. Buy a package to generate images.</span>
                <a href="{{ route('client.packages.index') }}" class="px-3 py-1.5 rounded-lg btn-primary text-white">View
                    Packages</a>
            </div>
        @endif
    @endauth
    @guest('client')
        <div class="mb-4 rounded-xl p-3 glass text-sm text-white/90 flex items-center justify-between">
            <span>Login to track your coins and purchases.</span>
            <div class="flex gap-2">
                <a href="{{ route('client.login') }}" class="px-3 py-1.5 rounded-lg glass hover:bg-white/10">Login</a>
                <a href="{{ route('client.register') }}"
                   class="px-3 py-1.5 rounded-lg btn-primary text-white">Register</a>
            </div>
        </div>
    @endguest
</div>