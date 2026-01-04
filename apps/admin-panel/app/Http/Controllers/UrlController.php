<?php

namespace App\Http\Controllers;

// use App\Services\UrlShortnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrlController
{
    public function index()
    {
        $urls = DB::table('urls')
            ->latest()
            ->paginate(20);

        return view('urls.index', compact('urls'));
    }

    public function create()
    {
        return view('urls.create');
    }

    // public function store(Request $request, UrlShortnerService $service)
    // {
    //     $request->validate([
    //         'original_url' => ['required', 'url', 'max:2048'],
    //     ]);

    //     $service->create(
    //         $request->original_url,
    //         auth()->id()
    //     );

    //     return redirect()
    //         ->route('urls.index')
    //         ->with('success', 'Short URL created');
    // }
}
