<?php

namespace App\Http\Controllers\Internal;

use Illuminate\Http\Request;
use App\Services\UrlMappingService;

class ShortenController
{
    public function show(UrlMappingService $service, string $id)
    {
        $result = $service->find($id);

        return response()->json([
            'mapping' => $result,
        ]);
    }
    public function store(Request $request, UrlMappingService $service)
    {
        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'tenant_id'    => ['required', 'integer'],
            'created_by'   => ['required', 'integer'],
            'method'       => ['required', 'in:POST,GET,PUT,DELETE,PATCH'],
        ]);

        $result = $service->create(
            $validated['original_url'],
            $validated['tenant_id'],
            $validated['created_by'],
            $validated['method'],
        );

        return response()->json([
            'short_code' => $result['short_code'],
            'short_url'  => $result['short_url'],
        ], 201);
    }
}
