<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">
                    Halaman ini bersifat lihat saja. Role dan hak aksesnya sudah dirancang khusus di awal
                    (lewat seeder) supaya konsisten dan aman &mdash; untuk mengubah struktur permission, hubungi developer.
                </p>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nama Role</th>
                            <th class="py-2">Jumlah Permission</th>
                            <th class="py-2">Jumlah User</th>
                            <th class="py-2 w-32">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr class="border-b">
                                <td class="py-2 font-medium">{{ $role->name }}</td>
                                <td class="py-2">{{ $role->permissions_count }}</td>
                                <td class="py-2">{{ $role->users_count }}</td>
                                <td class="py-2">
                                    <a href="{{ route('roles.show', $role) }}" class="text-indigo-600 hover:underline">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
