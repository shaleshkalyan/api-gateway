<x-layout>
    <div class="container-fluid bg-light min-vh-100 d-flex align-items-center justify-content-center py-5">
        
        <div class="card shadow-lg" style="max-width: 400px; width: 100%;">
            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <h2 class="h3 fw-bold text-dark">Admin Login</h2>
                    <p class="text-muted">Sign in to manage your URL services</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> 
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="emailInput" class="form-label">Email</label>
                        <input id="emailInput" type="email" name="email" class="form-control" required 
                               value="{{ old('email') }}" placeholder="name@example.com">
                    </div>

                    <div class="mb-4">
                        <label for="passwordInput" class="form-label">Password</label>
                        <input id="passwordInput" type="password" name="password" class="form-control" required 
                               placeholder="••••••••">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-layout>