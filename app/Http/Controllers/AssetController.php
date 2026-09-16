<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LocationArea;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('category', 'locationArea')->latest()->paginate(10);

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        $locationAreas = LocationArea::with('floor.building.site')->orderBy('name')->get();

        return view('assets.create', compact('categories', 'locationAreas'));
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        $data['asset_code'] = Asset::generateAssetCode();

        Asset::create($data);

        return redirect()->route('assets.index')->with('success', 'Asset berhasil ditambahkan.');
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::orderBy('name')->get();
        $locationAreas = LocationArea::with('floor.building.site')->orderBy('name')->get();

        return view('assets.edit', compact('asset', 'categories', 'locationAreas'));
    }

    public function update(StoreAssetRequest $request, Asset $asset)
    {
        $asset->update($request->validated());

        return redirect()->route('assets.index')->with('success', 'Asset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset berhasil dihapus.');
    }
}
