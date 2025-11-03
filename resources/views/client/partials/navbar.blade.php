<nav class="sticky top-0 z-40 backdrop-blur glass/50 border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        {{-- Left: Brand + primary links --}}
        <div class="flex items-center gap-6">
            <a href="{{ url('/') }}" class="text-white/90 hover:text-white font-semibold">Alice Studio</a>
            <a href="{{ route('client.packages.index') }}" class="text-white/70 hover:text-white text-sm">Packages</a>
            {{-- (Optional) Your creator home/page --}}
            <a href="{{ url('/creator') }}" class="text-white/70 hover:text-white text-sm">Creator</a>
        </div>

        {{-- Right: Auth/User --}}
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
