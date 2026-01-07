<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container-fluid">
            
            <a class="navbar-brand fw-bold text-white me-5" href="{{ route('dashboard') }}">
                {{ config('app.name') }}
            </a>

            @auth
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               aria-current="{{ request()->routeIs('dashboard') ? 'page' : '' }}">
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ url('tenants') }}"
                               class="nav-link {{ request()->is('tenants*') ? 'active' : '' }}"
                               aria-current="{{ request()->is('tenants*') ? 'page' : '' }}">
                                API Clients
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ url('urls') }}"
                               class="nav-link {{ request()->is('urls*') ? 'active' : '' }}"
                               aria-current="{{ request()->is('urls*') ? 'page' : '' }}">
                                APIs
                            </a>
                        </li>
                    </ul>

                    <div class="d-flex">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </nav>
</header>