<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiteRequest;
use App\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount('buildings')->latest()->paginate(10);

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function store(StoreSiteRequest $request)
    {
        Site::create($request->validated());

        return redirect()->route('sites.index')->with('success', 'Site berhasil ditambahkan.');
    }

    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    public function update(StoreSiteRequest $request, Site $site)
    {
        $site->update($request->validated());

        return redirect()->route('sites.index')->with('success', 'Site berhasil diperbarui.');
    }

    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site berhasil dihapus.');
    }
}
