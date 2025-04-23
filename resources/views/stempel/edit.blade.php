<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Stempel') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form method="POST" action="{{ route('stempels.update', $stempel) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700">Nama Stempel</label>
                        <input type="text" name="nama" value="{{ $stempel->nama }}" class="w-full mt-1 p-2 border border-gray-300 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Gambar Saat Ini</label>
                        <img src="{{ asset('storage/' . $stempel->gambar) }}" class="h-24 mb-2">
                        <input type="file" name="gambar" class="w-full mt-1 p-2 border border-gray-300 rounded">
                        <small class="text-gray-500">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
