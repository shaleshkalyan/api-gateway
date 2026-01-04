<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body>

<x-header />

<main class="container">
    <div class="card">
        {{ $slot }}
    </div>
</main>

<x-footer />

</body>
</html>
