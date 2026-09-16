<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Floors</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('floors.create') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Tambah Floor</a>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Kode</th>
                            <th class="py-2">Building</th>
                            <th class="py-2">Jumlah Location Area</th>
                            <th class="py-2 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($floors as $floor)
                            <tr class="border-b">
                                <td class="py-2">{{ $floor->name }}</td>
                                <td class="py-2">{{ $floor->code }}</td>
                                <td class="py-2">{{ $floor->building->name }}</td>
                                <td class="py-2">{{ $floor->location_areas_count }}</td>
                                <td class="py-2 space-x-2">
                                    <a href="{{ route('floors.edit', $floor) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('floors.destroy', $floor) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus floor ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">Belum ada data floor.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $floors->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
