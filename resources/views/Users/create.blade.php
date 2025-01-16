<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah User') }}
            </h2>
        </div>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-8">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" name="name" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="password" name="password" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            @error('password')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar (Opsional)</label>
                            <input type="file" id="avatar" name="avatar" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <small class="text-gray-500">Kosongkan jika ingin menggunakan avatar default.</small>
                            @error('avatar')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- NIM -->
                        <div class="hidden" id="nim-field">
                            <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                            <input type="text" id="nim" name="nim" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('nim') }}">
                            @error('nim')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- NIDN -->
                        <div class="hidden" id="nidn-field">
                            <label for="nidn" class="block text-sm font-medium text-gray-700">NIDN</label>
                            <input type="text" id="nidn" name="nidn" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('nidn') }}">
                            @error('nidn')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- NIP -->
                        <div class="hidden" id="nip-field">
                            <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                            <input type="text" id="nip" name="nip" class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('nip') }}">
                            @error('nip')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Roles -->
                        <div>
                            <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
                            <select id="roles" name="roles[]" multiple class="mt-2 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('roles')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">Simpan</button>
                        <a href="{{ route('users.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow-sm hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const rolesSelect = document.getElementById('roles');
    const nimField = document.getElementById('nim-field');
    const nidnField = document.getElementById('nidn-field');
    const nipField = document.getElementById('nip-field');

    function updateFieldVisibility() {
        const selectedRoles = Array.from(rolesSelect.selectedOptions).map(option => option.value);

        // Tampilkan kolom NIM jika role 'mahasiswa' dipilih
        nimField.classList.toggle('hidden', !selectedRoles.includes('mahasiswa'));

        // Tampilkan kolom NIDN dan NIP jika role 'adminprodi' atau 'kepalaprodi' dipilih
        const isProdi = selectedRoles.includes('adminprodi') || selectedRoles.includes('kepalaprodi');
        nidnField.classList.toggle('hidden', !isProdi);
        nipField.classList.toggle('hidden', !isProdi);
    }

    // Jalankan fungsi saat ada perubahan pada select
    rolesSelect.addEventListener('change', updateFieldVisibility);

    // Jalankan fungsi saat halaman pertama kali dimuat
    updateFieldVisibility();
});

    </script>
</x-app-layout>
