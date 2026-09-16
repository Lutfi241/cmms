<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingRequest;
use App\Models\Building;
use App\Models\Site;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::with('site')->withCount('floors')->latest()->paginate(10);

        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        $sites = Site::orderBy('name')->get();

        return view('buildings.create', compact('sites'));
    }

    public function store(StoreBuildingRequest $request)
    {
        Building::create($request->validated());

        return redirect()->route('buildings.index')->with('success', 'Building berhasil ditambahkan.');
    }

    public function edit(Building $building)
    {
        $sites = Site::orderBy('name')->get();

        return view('buildings.edit', compact('building', 'sites'));
    }

    public function update(StoreBuildingRequest $request, Building $building)
    {
        $building->update($request->validated());

        return redirect()->route('buildings.index')->with('success', 'Building berhasil diperbarui.');
    }

    public function destroy(Building $building)
    {
        $building->delete();

        return redirect()->route('buildings.index')->with('success', 'Building berhasil dihapus.');
    }
}
