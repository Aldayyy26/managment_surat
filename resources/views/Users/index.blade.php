<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Pengguna') }}
            </h2>
            <a href="{{ route('users.create') }}" class="mt-4 sm:mt-0 px-6 py-3 bg-indigo-600 text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300 text-center">
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="mb-4">
                <div class="flex justify-end">
                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap sm:flex-nowrap gap-4 items-center">
                        <input type="text" name="search_name" placeholder="Cari berdasarkan nama" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request()->get('search_name') }}">
                        <input type="text" name="search_nim" placeholder="Cari berdasarkan NIM" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request()->get('search_nim') }}">
                        <input type="text" name="search_nip" placeholder="Cari berdasarkan NIP" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request()->get('search_nip') }}">
                        <input type="text" name="search_nidn" placeholder="Cari berdasarkan NIDN" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request()->get('search_nidn') }}">
                        <select name="search_role" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Role</option>
                            @php
                                $rolesList = \Spatie\Permission\Models\Role::pluck('name');
                                $selectedRole = request()->get('search_role');
                            @endphp
                            @foreach ($rolesList as $role)
                                <option value="{{ $role }}" {{ $selectedRole == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                            Cari
                        </button>
                        @if(request()->filled('search_name') || request()->filled('search_nim') || request()->filled('search_nip') || request()->filled('search_nidn') || request()->filled('search_role'))
                            <a href="{{ route('users.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg font-semibold hover:bg-gray-500 transition duration-300">
                                Batal
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    @if($users->isNotEmpty())
                    <table class="min-w-full table-auto divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Nama</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Semester</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">NIP</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">NIDN</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Roles</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($user->hasRole('mahasiswa'))
                                        {{ $user->semester ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->nip ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->nidn ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @foreach ($user->roles as $role)
                                        {{ $role->name }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-full font-semibold hover:bg-indigo-700 transition duration-300">
                                            Edit
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-full font-semibold hover:bg-red-700 transition duration-300">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p class="text-center py-6 text-gray-500">Tidak ada data pengguna ditemukan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data ini akan dihapus dan tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
