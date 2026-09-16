<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFloorRequest;
use App\Models\Building;
use App\Models\Floor;

class FloorController extends Controller
{
    public function index()
    {
        $floors = Floor::with('building')->withCount('locationAreas')->latest()->paginate(10);

        return view('floors.index', compact('floors'));
    }

    public function create()
    {
        $buildings = Building::orderBy('name')->get();

        return view('floors.create', compact('buildings'));
    }

    public function store(StoreFloorRequest $request)
    {
        Floor::create($request->validated());

        return redirect()->route('floors.index')->with('success', 'Floor berhasil ditambahkan.');
    }

    public function edit(Floor $floor)
    {
        $buildings = Building::orderBy('name')->get();

        return view('floors.edit', compact('floor', 'buildings'));
    }

    public function update(StoreFloorRequest $request, Floor $floor)
    {
        $floor->update($request->validated());

        return redirect()->route('floors.index')->with('success', 'Floor berhasil diperbarui.');
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();

        return redirect()->route('floors.index')->with('success', 'Floor berhasil dihapus.');
    }
}
