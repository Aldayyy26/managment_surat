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
                <form method="GET" action="{{ route('users.index') }}" class="flex gap-4">
                    <input type="text" name="search_name" placeholder="Cari berdasarkan nama" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request()->get('search_name') }}">
                    <input type="text" name="search_nim" placeholder="Cari berdasarkan NIM" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request()->get('search_nim') }}">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                        Cari
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <!-- Make the container scrollable on small screens -->
                <div class="overflow-x-auto">
                    @foreach ($users as $user)
                    <div class="rounded-lg p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                        <!-- User Information -->
                        <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-6">
                            <!-- Name Section -->
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">Nama</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                            </div>

                            <!-- Email Section -->
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">Email</p>
                                <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                            </div>

                            <!-- Roles Section -->
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">Roles</p>
                                <p class="mt-1 text-gray-900">
                                    @foreach ($user->roles as $role)
                                    {{ $role->name }}@if (!$loop->last), @endif
                                    @endforeach
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 sm:mt-0 flex space-x-2">
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
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Prevent the default form submission

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
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
