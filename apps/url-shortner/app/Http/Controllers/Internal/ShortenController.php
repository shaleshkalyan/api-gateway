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

    public function update(Request $request, UrlMappingService $service, $id)
    {
        $validated = $request->validate([
            'original_url' => ['sometimes', 'url', 'max:2048'],
            'tenant_id'    => ['required', 'integer'],
            'method'       => ['sometimes', 'in:POST,GET,PUT,DELETE,PATCH'],
            'is_active'    => ['sometimes', 'boolean'],
        ]);
        
        // Pass the required ID, tenant_id, and all validated data to the service
        $service->update(
            $id,
            $validated['tenant_id'],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'URL mapping updated successfully',
        ]);
    }

    public function destroy(Request $request, UrlMappingService $service, $id)
    {
        $validated = $request->validate([
            'deleted_by' => ['required', 'integer'],
        ]);

        $service->delete(
            $id
        );

        return response()->json([
            'status' => 'success',
            'message' => 'URL mapping deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request, UrlMappingService $service)
    {
        $validated = $request->validate([
            'ids'        => ['required', 'array'],
            'ids.*'      => ['integer'],
            'deleted_by' => ['required', 'integer'],
        ]);

        $count = $service->bulkDelete(
            $validated['ids']
        );

        return response()->json([
            'status' => 'success',
            'deleted_count' => $count,
        ]);
    }

    public function bulkUpdate(Request $request, UrlMappingService $service)
    {
        $validated = $request->validate([
            'ids'          => ['required', 'array'],
            'ids.*'        => ['integer'],
            'original_url' => ['sometimes', 'url', 'max:2048'],
            'method'       => ['sometimes', 'in:POST,GET,PUT,DELETE,PATCH'],
            'is_active'    => ['sometimes', 'boolean'],
            'deleted_at'   => ['nullable'], 
        ]);
        
        // Remove IDs and user ID from data payload before passing to service
        $updateData = collect($validated)->except(['ids'])->toArray();

        $count = $service->bulkUpdate(
            $validated['ids'],
            $updateData
        );

        return response()->json([
            'status' => 'success',
            'updated_count' => $count,
        ]);
    }
}
