<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Stempel') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">

                <form method="POST" action="{{ route('stempels.update', $stempel->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700">Nama Stempel</label>
                        <input type="text" name="nama" value="{{ old('nama', $stempel->nama) }}" class="w-full mt-1 p-2 border border-gray-300 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Gambar Lama</label>
                        <img src="{{ asset('storage/' . $stempel->gambar) }}" alt="Stempel" class="w-32 mb-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Ganti Gambar (opsional)</label>
                        <input type="file" name="gambar" class="w-full mt-1 p-2 border border-gray-300 rounded">
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
