<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template Surat') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 text-red-600">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('surats.update', $template->id) }}" method="POST" class="bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block font-medium">Nama Surat</label>
                    <input type="text" name="nama_surat" value="{{ old('nama_surat', $template->nama_surat) }}" class="w-full border rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Jenis Pengguna</label>
                    <select name="user_type" class="w-full border rounded px-4 py-2" required>
                        <option value="">-- Pilih --</option>
                        <option value="mahasiswa" {{ old('user_type', $template->user_type) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('user_type', $template->user_type) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">File Surat (.docx)</label>
                    <p class="text-sm text-gray-600">Untuk mengganti file, silakan hapus dan upload template baru.</p>
                    <p class="mt-1"><strong>File saat ini:</strong> {{ basename($template->file_path) }}</p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
