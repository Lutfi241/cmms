<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Work Order</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="mb-4 text-sm text-gray-500">
                    Kode: <strong>{{ $workOrder->wo_code }}</strong> &middot;
                    Status saat ini: <strong>{{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}</strong>
                </p>

                <form action="{{ route('work_orders.update', $workOrder) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="asset_id" class="block text-sm font-medium text-gray-700">Asset</label>
                        <select name="asset_id" id="asset_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}" @selected(old('asset_id', $workOrder->asset_id) == $asset->id)>
                                    {{ $asset->asset_code }} - {{ $asset->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Masalah</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $workOrder->description) }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('work_orders.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
