<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orgs = Organization::all();
        if ($orgs->isEmpty()) {
            return apiResponse(false, 'No organizations found', [], 404);
        }
        return apiResponse(true, 'Organizations fetched successfully', $orgs);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => slugify($request->name),
        ]);
        return apiResponse(true, 'Organization created successfully', $organization, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $organization = Organization::find($id);
        if (!$organization) {
            return apiResponse(false, 'Organization not found', [], 404);
        }
        return apiResponse(true, 'Organization fetched successfully', $organization);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $organization = Organization::find($id);
        if (!$organization) {
            return apiResponse(false, 'Organization not found', [], 404);
        }

        $organization->update([
            'name' => $request->name,
            'slug' => slugify($request->name),
        ]);

        return apiResponse(true, 'Organization updated successfully', $organization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $organization = Organization::find($id);
        if (!$organization) {
            return apiResponse(false, 'Organization not found', [], 404);
        }

        $organization->delete();
        return apiResponse(true, 'Organization deleted successfully');
    }
}
