<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kop Surat') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('kop.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Kop Surat (Gambar)</label>
                        <input type="file" name="gambar" class="w-full mt-1 p-2 border border-gray-300 rounded">
                        @if($kop && $kop->gambar)
                            <img src="{{ asset('storage/' . $kop->gambar) }}" class="mt-2 max-w-full h-auto rounded shadow">
                        @endif
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
