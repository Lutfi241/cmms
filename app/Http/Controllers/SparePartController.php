<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSparePartRequest;
use App\Models\SparePart;

class SparePartController extends Controller
{
    public function index()
    {
        $spareParts = SparePart::orderBy('name')->paginate(10);

        return view('spare_parts.index', compact('spareParts'));
    }

    public function create()
    {
        return view('spare_parts.create');
    }

    public function store(StoreSparePartRequest $request)
    {
        SparePart::create($request->validated());

        return redirect()->route('spare_parts.index')->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function edit(SparePart $spare_part)
    {
        return view('spare_parts.edit', ['sparePart' => $spare_part]);
    }

    public function update(StoreSparePartRequest $request, SparePart $spare_part)
    {
        $spare_part->update($request->validated());

        return redirect()->route('spare_parts.index')->with('success', 'Spare part berhasil diperbarui.');
    }

    public function destroy(SparePart $spare_part)
    {
        $spare_part->delete();

        return redirect()->route('spare_parts.index')->with('success', 'Spare part berhasil dihapus.');
    }
}
