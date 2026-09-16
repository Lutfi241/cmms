<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Work Order {{ $workOrder->wo_code }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info Work Order --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Asset</p>
                        <p class="font-medium">{{ $workOrder->asset->asset_code }} - {{ $workOrder->asset->name }}</p>
                    </div>
                    <span class="px-3 py-1 rounded text-sm {{ $workOrder->statusColor() }}">
                        {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-500">Requester</p>
                        <p>{{ $workOrder->requester->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Teknisi</p>
                        <p>{{ $workOrder->technician->name ?? 'Belum ditugaskan' }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Deskripsi</p>
                    <p>{{ $workOrder->description }}</p>
                </div>
                @if ($workOrder->status === 'rejected' && $workOrder->rejection_reason)
                    <div class="mt-4 p-3 bg-red-50 text-red-700 text-sm rounded">
                        Alasan ditolak: {{ $workOrder->rejection_reason }}
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('work_orders.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke daftar</a>
                </div>
            </div>

            {{-- Penugasan Teknisi (untuk Admin Site, Manager, Supervisor Maintenance & Super Admin) --}}
            @can('assign_work_orders')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Penugasan Teknisi</h3>

                    @if (in_array($workOrder->status, ['approved', 'in_progress']))
                        <form action="{{ route('work_orders.assign', $workOrder) }}" method="POST" class="flex items-end space-x-2">
                            @csrf
                            <div class="flex-1">
                                <label for="technician_id" class="block text-sm font-medium text-gray-700">Teknisi Penanggung Jawab</label>
                                <select name="technician_id" id="technician_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Belum ditugaskan (siapa pun boleh mengambil) --</option>
                                    @foreach ($technicians as $technician)
                                        <option value="{{ $technician->id }}" @selected($workOrder->technician_id == $technician->id)>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Simpan
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-2">
                            Jika ditugaskan ke teknisi tertentu, hanya teknisi tersebut yang bisa mengerjakan Work Order ini.
                        </p>
                    @else
                        <p class="text-sm text-gray-400">
                            Teknisi hanya bisa ditugaskan saat Work Order berstatus "approved" atau "in progress".
                            Status saat ini: <strong>{{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}</strong>.
                        </p>
                    @endif
                </div>
            @endcan

            {{-- Daftar Spare Part Terpakai --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Spare Part Terpakai</h3>

                <table class="w-full text-left border-collapse mb-4">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nama Part</th>
                            <th class="py-2">Nomor Part</th>
                            <th class="py-2">Jumlah</th>
                            @can('execute_work_orders')
                                @if ($workOrder->status === 'in_progress')
                                    <th class="py-2 w-24">Aksi</th>
                                @endif
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($workOrder->parts as $used)
                            <tr class="border-b">
                                <td class="py-2">{{ $used->sparePart->name }}</td>
                                <td class="py-2">{{ $used->sparePart->part_number }}</td>
                                <td class="py-2">{{ $used->quantity }} {{ $used->sparePart->unit }}</td>
                                @can('execute_work_orders')
                                    @if ($workOrder->status === 'in_progress')
                                        <td class="py-2">
                                            <form action="{{ route('work_orders.parts.remove', [$workOrder, $used]) }}" method="POST"
                                                  onsubmit="return confirm('Batalkan pemakaian part ini? Stok akan dikembalikan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-sm">Batalkan</button>
                                            </form>
                                        </td>
                                    @endif
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-gray-500">Belum ada spare part yang dipakai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @can('execute_work_orders')
                    @if ($workOrder->status === 'in_progress')
                        <form action="{{ route('work_orders.parts.add', $workOrder) }}" method="POST" class="flex items-end space-x-2 border-t pt-4">
                            @csrf
                            <div class="flex-1">
                                <label for="spare_part_id" class="block text-sm font-medium text-gray-700">Spare Part</label>
                                <select name="spare_part_id" id="spare_part_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Pilih Spare Part --</option>
                                    @foreach ($spareParts as $part)
                                        <option value="{{ $part->id }}">
                                            {{ $part->name }} ({{ $part->part_number }}) - Stok: {{ $part->stock }} {{ $part->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32">
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input type="number" name="quantity" id="quantity" min="1" value="1"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Tambah
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-400 border-t pt-4">
                            Spare part hanya bisa ditambahkan saat Work Order berstatus "in_progress".
                        </p>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
