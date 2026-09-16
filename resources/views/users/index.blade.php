<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @can('create_users')
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('users.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Tambah User</a>
                    </div>
                @endcan

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Email</th>
                            <th class="py-2">Role</th>
                            <th class="py-2 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b">
                                <td class="py-2">
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <span class="text-xs text-gray-400">(kamu)</span>
                                    @endif
                                </td>
                                <td class="py-2">{{ $user->email }}</td>
                                <td class="py-2">
                                    @foreach ($user->roles as $role)
                                        <span class="px-2 py-1 rounded text-xs bg-indigo-100 text-indigo-800">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="py-2 space-x-2">
                                    @can('edit_users')
                                        <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('delete_users')
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Yakin hapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-gray-500">Belum ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
