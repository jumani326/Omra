<aside class="sidebar text-white" id="sidebar">
    <div class="p-4">
        <!-- Logo -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-xl font-bold font-poppins" id="sidebar-logo">UMS</h1>
            <button id="sidebar-toggle" class="text-white hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('dashboard') ? 'bg-dark-green' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="sidebar-text">Dashboard</span>
            </a>
            
            @can('view-pilgrims')
            @if(Route::has('pilgrims.index'))
            <a href="{{ route('pilgrims.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('pilgrims.*') ? 'bg-dark-green' : '' }}">
            @else
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition opacity-50 cursor-not-allowed" title="Module à venir - Jour 3">
            @endif
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="sidebar-text">Pèlerins</span>
            </a>
            @endcan
            
            @can('view-packages')
            @if(Route::has('packages.index'))
            <a href="{{ route('packages.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('packages.*') ? 'bg-dark-green' : '' }}">
            @else
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition opacity-50 cursor-not-allowed" title="Module à venir - Jour 3">
            @endif
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="sidebar-text">Forfaits</span>
            </a>
            @endcan
            
            @can('viewAny', App\Models\Hotel::class)
            @if(Route::has('hotels.index'))
            <a href="{{ route('hotels.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('hotels.*') ? 'bg-dark-green' : '' }}">
            @else
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition opacity-50 cursor-not-allowed" title="Module à venir">
            @endif
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="sidebar-text">Hôtels</span>
            </a>
            @endcan
            
            @can('view-visas')
            @if(Route::has('visas.index'))
            <a href="{{ route('visas.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('visas.*') ? 'bg-dark-green' : '' }}">
            @else
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition opacity-50 cursor-not-allowed" title="Module à venir - Jour 4">
            @endif
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="sidebar-text">Visas</span>
            </a>
            @endcan
            
            @can('view-payments')
            @if(Route::has('payments.index'))
            <a href="{{ route('payments.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('payments.*') ? 'bg-dark-green' : '' }}">
            @else
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition opacity-50 cursor-not-allowed" title="Module à venir - Jour 4">
            @endif
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="sidebar-text">Finance</span>
            </a>
            @endcan
            
            @can('view-users')
            @if(Route::has('users.index'))
            <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition {{ request()->routeIs('users.*') ? 'bg-dark-green' : '' }}">
            @else
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-dark-green transition opacity-50 cursor-not-allowed" title="Module à venir - Jour 2">
            @endif
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="sidebar-text">Utilisateurs</span>
            </a>
            @endcan
        </nav>
    </div>
</aside>

<script>
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Restaurer l'état du sidebar
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        document.getElementById('sidebar').classList.add('collapsed');
    }
</script>

<style>
    .sidebar.collapsed .sidebar-text {
        display: none;
    }
    
    .sidebar.collapsed #sidebar-logo {
        font-size: 0.75rem;
    }
</style>

