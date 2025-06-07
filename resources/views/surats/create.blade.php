<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Template Surat') }}
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

            <form action="{{ route('surats.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block font-medium">Nomor Jenis Surat</label>
                    <input type="text" name="no_jenis_surat" class="w-full border rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Nama Surat</label>
                    <input type="text" name="nama_surat" class="w-full border rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Jenis Pengguna</label>
                    <select name="user_type" class="w-full border rounded px-4 py-2" required>
                        <option value="" disabled selected>Pilih jenis pengguna yg bisa mengajukan template</option>
                        @foreach ($userRoles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium">File Surat (.docx)</label>
                    <input type="file" name="file_surat" accept=".docx" class="w-full border rounded px-4 py-2" required>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Upload dan Proses
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
