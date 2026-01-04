<header class="header">
    <div class="header-inner">
        <strong>{{ config('app.name') }}</strong>

        @auth
            <nav>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('tenants.index') }}">Tenants</a>
                <a href="{{ route('url.index') }}">URLs</a>
                <a href="{{ route('url.create') }}">Create URL</a>

                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button class="btn btn-danger">Logout</button>
                </form>
            </nav>
        @endauth
    </div>
</header>
