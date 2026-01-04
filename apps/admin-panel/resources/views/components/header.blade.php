<header class="header">
    <div class="header-inner">
        <strong>{{ config('app.name') }}</strong>

        @auth
            <nav>
                <a href="{{ route('dashboard') }}">Dashboard</a>

                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button class="btn btn-danger">Logout</button>
                </form>
            </nav>
        @endauth
    </div>
</header>
