<aside class="sidebar text-white" id="sidebar">
    <div class="p-4 flex flex-col h-full">
        @php
            $unreadNotifications = auth()->check()
                ? \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count()
                : 0;
        @endphp
        @role('pelerin')
        {{-- Visuel demandé : logo type Laravel (boîte dorée + cube) + UMRAH SERVICES + hamburger --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-10 h-10 rounded-md bg-[var(--gold-accent)] flex items-center justify-center flex-shrink-0 shadow-md p-1.5">
                    <svg class="w-full h-full text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 7v10"/><path d="M12 2v10"/><path d="M22 7v10"/></svg>
                </div>
                <p class="font-bold text-white truncate uppercase tracking-wide text-sm sidebar-text" id="sidebar-logo">Umrah Services</p>
            </div>
            <button id="sidebar-toggle" class="text-white/80 hover:text-white flex-shrink-0 p-1 rounded hover:bg-white/10" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        @php
            $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
        @endphp
        <nav class="space-y-1 flex-1">
            <a href="{{ route('pelerin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('pelerin.dashboard') ? 'bg-green-600/80 text-white' : 'hover:bg-white/10 text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('client.packages.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.*') ? 'bg-green-600/80 text-white' : 'hover:bg-white/10 text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span class="sidebar-text">Packages</span>
            </a>
            <a href="{{ route('pelerin.notifications.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('pelerin.notifications.*') ? 'bg-green-600/80 text-white' : 'hover:bg-white/10 text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="sidebar-text flex items-center gap-1">
                    Notifications
                    @if($unreadNotifications > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-amber-400 text-primary-green">
                            {{ $unreadNotifications }}
                        </span>
                    @endif
                </span>
            </a>
            <a href="{{ route('pelerin.messages.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('pelerin.messages.*') ? 'bg-green-600/80 text-white' : 'hover:bg-white/10 text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span class="sidebar-text">Messagerie</span>
            </a>
            <a href="{{ route('pelerin.dashboard') }}#documents" class="flex items-center px-4 py-3 rounded-lg hover:bg-white/10 text-white transition">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="sidebar-text">Documents</span>
            </a>
            <a href="{{ route('pelerin.dashboard') }}#procedure" class="flex items-center px-4 py-3 rounded-lg hover:bg-white/10 text-white transition">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span class="sidebar-text">Procedure</span>
            </a>
            <a href="{{ route('pelerin.profile') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-white/10 text-white transition {{ request()->routeIs('pelerin.profile') ? 'bg-green-600/80' : '' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="sidebar-text">Profile</span>
            </a>
        </nav>
        <div class="pt-4 mt-auto border-t border-white/20">
            <p class="text-white/80 text-xs sidebar-text">Support ID</p>
            <p class="text-[var(--gold-accent)] font-mono font-semibold text-sm mt-0.5 sidebar-text">UMS-{{ auth()->id() }}-{{ date('y') }}</p>
        </div>
        @else
        {{-- Autres rôles : logo UMS + toggle --}}
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-xl font-bold font-poppins" id="sidebar-logo">UMS</h1>
            <button id="sidebar-toggle" class="text-white hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        <nav class="space-y-2">
            @role('guide')
                {{-- Menu GUIDE : Mon Groupe + Messagerie --}}
                <a href="{{ route('guide.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('guide.dashboard') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="sidebar-text">Mon groupe</span>
                </a>
                <a href="{{ route('guide.messages.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('guide.messages.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="sidebar-text">Messagerie</span>
                </a>
            @endrole

            @role('ministere')
                {{-- Menu MINISTÈRE --}}
                <a href="{{ route('ministere.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('ministere.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            @endrole

            @role('agence')
                {{-- Menu AGENCE : un seul Dashboard + Pèlerins, Forfaits, Hôtels, Visas, Comptabilité, Utilisateurs --}}
                <a href="{{ route('agence.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('agence.dashboard') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="{{ route('agence.notifications.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('agence.notifications.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="sidebar-text flex items-center gap-1">
                        Notifications
                        @if($unreadNotifications > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-amber-400 text-primary-green">
                                {{ $unreadNotifications }}
                            </span>
                        @endif
                    </span>
                </a>
                <a href="{{ route('pilgrims.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('pilgrims.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="sidebar-text">Pèlerins</span>
                </a>
                <a href="{{ route('guides.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('guides.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="sidebar-text">Guides</span>
                </a>
                <a href="{{ route('groups.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('groups.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="sidebar-text">Groupes</span>
                </a>
                <a href="{{ route('packages.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('packages.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="sidebar-text">Forfaits</span>
                </a>
                <a href="{{ route('hotels.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('hotels.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="sidebar-text">Hôtels</span>
                </a>
                <a href="{{ route('visas.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('visas.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="sidebar-text">Visas</span>
                </a>
                <a href="{{ route('comptabilite.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('comptabilite.*') || request()->routeIs('compte-marchands.*') || request()->routeIs('transaction-digitales.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="sidebar-text">Comptabilité</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('users.*') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="sidebar-text">Utilisateurs</span>
                </a>
                <a href="{{ route('agence.profile') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('agence.profile') ? 'bg-dark-green' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="sidebar-text">Profil</span>
                </a>
            @endrole
        </nav>
        @endrole
    </div>
</aside>

<script>
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        document.getElementById('sidebar').classList.add('collapsed');
    }
</script>

<style>
    .sidebar.collapsed .sidebar-text { display: none; }
    .sidebar.collapsed #sidebar-logo { font-size: 0.75rem; }
</style>
