<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Dashboard/Links', [
            'links' => Link::orderBy('created_at', 'desc')->get(),
            'templates' => Template::orderBy('name')->get(),
            'countries' => Link::countryCollection(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:links,slug',
            'target_urls' => 'required|string',
            'campaign_method' => 'required|string|in:redirect,landingpage',
            'template' => 'required|string|max:255',
            'lock_country' => 'nullable|string',
            'lock_device' => 'nullable|string',
            'lock_browser' => 'nullable|string',
            'active' => 'required|boolean',
        ]);

        Link::create($validated);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Link $link): RedirectResponse
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:links,slug,' . $link->id,
            'target_urls' => 'required|string',
            'campaign_method' => 'required|string|in:redirect,landingpage',
            'template' => 'required|string|max:255',
            'lock_country' => 'nullable|string',
            'lock_device' => 'nullable|string',
            'lock_browser' => 'nullable|string',
            'active' => 'required|boolean',
        ]);

        $link->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();

        return redirect()->back();
    }

    /**
     * Toggle the active state.
     */
    public function toggleActive(Request $request, Link $link): RedirectResponse
    {
        $validated = $request->validate([
            'active' => 'required|boolean',
        ]);

        $link->update([
            'active' => $validated['active'],
        ]);

        return redirect()->back();
    }
}
