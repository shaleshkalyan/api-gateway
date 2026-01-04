<x-layout>
    <h2>Admin Login</h2>

    @if ($errors->any())
        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="form-group">
            <label class="label">Email</label>
            <input class="input" type="email" name="email" required>
        </div>

        <div class="form-group">
            <label class="label">Password</label>
            <input class="input" type="password" name="password" required>
        </div>

        <button class="btn">Login</button>
    </form>
</x-layout>
