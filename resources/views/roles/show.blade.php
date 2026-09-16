<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Role: {{ $role->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('roles.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke daftar role</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Permission ({{ $role->permissions->count() }})</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse ($role->permissions as $permission)
                        <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">{{ $permission->name }}</span>
                    @empty
                        <p class="text-gray-500 text-sm">Role ini belum punya permission.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">User dengan Role Ini ({{ $role->users->count() }})</h3>
                <ul class="list-disc list-inside text-sm">
                    @forelse ($role->users as $user)
                        <li>{{ $user->name }} ({{ $user->email }})</li>
                    @empty
                        <li class="list-none text-gray-500">Belum ada user dengan role ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
