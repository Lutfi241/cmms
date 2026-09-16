<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assets</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('assets.create') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Tambah Asset</a>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Kode</th>
                            <th class="py-2">Nama</th>
                            <th class="py-2">Kategori</th>
                            <th class="py-2">Lokasi</th>
                            <th class="py-2">Status</th>
                            <th class="py-2 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $asset)
                            <tr class="border-b">
                                <td class="py-2">{{ $asset->asset_code }}</td>
                                <td class="py-2">{{ $asset->name }}</td>
                                <td class="py-2">{{ $asset->category->name }}</td>
                                <td class="py-2">{{ $asset->locationArea->name }}</td>
                                <td class="py-2">
                                    <span @class([
                                        'px-2 py-1 rounded text-xs',
                                        'bg-green-100 text-green-800' => $asset->status === 'active',
                                        'bg-yellow-100 text-yellow-800' => $asset->status === 'maintenance',
                                        'bg-gray-100 text-gray-800' => $asset->status === 'retired',
                                    ])>
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td class="py-2 space-x-2">
                                    <a href="{{ route('assets.edit', $asset) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus asset ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-gray-500">Belum ada data asset.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $assets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
