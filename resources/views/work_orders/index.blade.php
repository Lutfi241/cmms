<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Work Orders</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @can('create_work_orders')
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('work_orders.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Ajukan Work Order</a>
                    </div>
                @endcan

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Kode</th>
                            <th class="py-2">Asset</th>
                            <th class="py-2">Deskripsi</th>
                            <th class="py-2">Requester</th>
                            <th class="py-2">Teknisi</th>
                            <th class="py-2">Status</th>
                            <th class="py-2 w-64">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($workOrders as $wo)
                            <tr class="border-b align-top">
                                <td class="py-2">
                                    <a href="{{ route('work_orders.show', $wo) }}" class="text-indigo-600 hover:underline">{{ $wo->wo_code }}</a>
                                </td>
                                <td class="py-2">{{ $wo->asset->name }}</td>
                                <td class="py-2 max-w-xs">
                                    {{ \Illuminate\Support\Str::limit($wo->description, 60) }}
                                    @if ($wo->status === 'rejected' && $wo->rejection_reason)
                                        <div class="text-xs text-red-600 mt-1">Alasan ditolak: {{ $wo->rejection_reason }}</div>
                                    @endif
                                </td>
                                <td class="py-2">{{ $wo->requester->name ?? '-' }}</td>
                                <td class="py-2">{{ $wo->technician->name ?? '-' }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 rounded text-xs {{ $wo->statusColor() }}">
                                        {{ ucfirst(str_replace('_', ' ', $wo->status)) }}
                                    </span>
                                </td>
                                <td class="py-2 space-y-1">
                                    <div class="space-x-2">
                                        @can('edit_work_orders')
                                            <a href="{{ route('work_orders.edit', $wo) }}" class="text-indigo-600 hover:underline">Edit</a>
                                        @endcan
                                        @can('delete_work_orders')
                                            <form action="{{ route('work_orders.destroy', $wo) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Yakin hapus work order ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        @endcan
                                    </div>

                                    @can('approve_work_orders')
                                        @if ($wo->status === 'pending')
                                            <div class="flex space-x-1">
                                                <form action="{{ route('work_orders.approve', $wo) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Setujui</button>
                                                </form>
                                                <button type="button" onclick="document.getElementById('reject-form-{{ $wo->id }}').classList.toggle('hidden')"
                                                        class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Tolak</button>
                                            </div>
                                            <form id="reject-form-{{ $wo->id }}" action="{{ route('work_orders.reject', $wo) }}" method="POST" class="hidden mt-1">
                                                @csrf
                                                <input type="text" name="rejection_reason" placeholder="Alasan penolakan"
                                                       class="text-xs border-gray-300 rounded w-full mb-1" required>
                                                <button type="submit" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Kirim Penolakan</button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('execute_work_orders')
                                        @if ($wo->status === 'approved')
                                            <form action="{{ route('work_orders.start', $wo) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">Mulai Kerjakan</button>
                                            </form>
                                        @elseif ($wo->status === 'in_progress')
                                            <form action="{{ route('work_orders.complete', $wo) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Selesaikan</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-4 text-gray-500">Belum ada data work order.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $workOrders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
