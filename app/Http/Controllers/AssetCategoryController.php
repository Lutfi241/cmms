<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetCategoryRequest;
use App\Models\AssetCategory;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $assetCategories = AssetCategory::latest()->paginate(10);

        return view('asset_categories.index', compact('assetCategories'));
    }

    public function create()
    {
        return view('asset_categories.create');
    }

    public function store(StoreAssetCategoryRequest $request)
    {
        AssetCategory::create($request->validated());

        return redirect()->route('asset_categories.index')
            ->with('success', 'Kategori aset berhasil ditambahkan.');
    }

    public function edit(AssetCategory $asset_category)
    {
        return view('asset_categories.edit', ['assetCategory' => $asset_category]);
    }

    public function update(StoreAssetCategoryRequest $request, AssetCategory $asset_category)
    {
        $asset_category->update($request->validated());

        return redirect()->route('asset_categories.index')
            ->with('success', 'Kategori aset berhasil diperbarui.');
    }

    public function destroy(AssetCategory $asset_category)
    {
        $asset_category->delete();

        return redirect()->route('asset_categories.index')
            ->with('success', 'Kategori aset berhasil dihapus.');
    }
}
