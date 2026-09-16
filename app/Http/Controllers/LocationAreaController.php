<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationAreaRequest;
use App\Models\Floor;
use App\Models\LocationArea;

class LocationAreaController extends Controller
{
    public function index()
    {
        $locationAreas = LocationArea::with('floor')->withCount('assets')->latest()->paginate(10);

        return view('location_areas.index', compact('locationAreas'));
    }

    public function create()
    {
        $floors = Floor::orderBy('name')->get();

        return view('location_areas.create', compact('floors'));
    }

    public function store(StoreLocationAreaRequest $request)
    {
        LocationArea::create($request->validated());

        return redirect()->route('location_areas.index')->with('success', 'Location Area berhasil ditambahkan.');
    }

    public function edit(LocationArea $location_area)
    {
        $floors = Floor::orderBy('name')->get();

        return view('location_areas.edit', ['locationArea' => $location_area, 'floors' => $floors]);
    }

    public function update(StoreLocationAreaRequest $request, LocationArea $location_area)
    {
        $location_area->update($request->validated());

        return redirect()->route('location_areas.index')->with('success', 'Location Area berhasil diperbarui.');
    }

    public function destroy(LocationArea $location_area)
    {
        $location_area->delete();

        return redirect()->route('location_areas.index')->with('success', 'Location Area berhasil dihapus.');
    }
}
