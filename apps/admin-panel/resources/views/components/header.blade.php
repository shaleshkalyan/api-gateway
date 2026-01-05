<header class="header">
    <div class="header-inner">
        <strong>{{ config('app.name') }}</strong>

        @auth
            <nav class="nav">
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                <a href="{{ url('tenants') }}"
                   class="{{ request()->is('tenants*') ? 'active' : '' }}">
                    Tenants
                </a>

                <a href="{{ url('url') }}"
                   class="{{ request()->is('url*') ? 'active' : '' }}">
                    URLs
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </nav>
        @endauth
    </div>
</header>
