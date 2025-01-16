<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pengguna') }}
            </h2>
            <a href="{{ route('users.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-sm hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                        <p class="mt-1 block text-sm text-gray-800">{{ $user->name }}</p>
                    </div>

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 block text-sm text-gray-800">{{ $user->email }}</p>
                    </div>

                    <div class="mb-6">
                        <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                        <p class="mt-1 block text-sm text-gray-800">{{ $user->nim ?? '-' }}</p>
                    </div>

                    <div class="mb-6">
                        <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar</label>
                        <div class="mt-1">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-32 h-32 object-cover rounded-full">
                            @else
                                <span class="text-gray-500">Avatar tidak tersedia</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
                        <div class="mt-1">
                            @foreach ($user->roles as $role)
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <a href="{{ route('users.edit', $user->id) }}" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">Edit</a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-sm hover:bg-red-700 focus:ring-2 focus:ring-red-500">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
