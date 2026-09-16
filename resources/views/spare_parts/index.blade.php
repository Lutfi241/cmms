<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Spare Parts</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @can('create_spare_parts')
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('spare_parts.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Tambah Spare Part</a>
                    </div>
                @endcan

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Nomor Part</th>
                            <th class="py-2">Stok</th>
                            <th class="py-2">Satuan</th>
                            <th class="py-2 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spareParts as $part)
                            <tr class="border-b">
                                <td class="py-2">{{ $part->name }}</td>
                                <td class="py-2">{{ $part->part_number }}</td>
                                <td class="py-2">
                                    <span class="{{ $part->isLowStock() ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $part->stock }}
                                    </span>
                                    @if ($part->isLowStock())
                                        <span class="ml-1 px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Stok Menipis</span>
                                    @endif
                                </td>
                                <td class="py-2">{{ $part->unit }}</td>
                                <td class="py-2 space-x-2">
                                    @can('edit_spare_parts')
                                        <a href="{{ route('spare_parts.edit', $part) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('delete_spare_parts')
                                        <form action="{{ route('spare_parts.destroy', $part) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin hapus spare part ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">Belum ada data spare part.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $spareParts->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
