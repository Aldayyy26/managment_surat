<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Kop Surat') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <a href="{{ route('kop.edit') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Tambah Kop Surat
                    </a>
                </div>

                <div class="mb-4">
                    <h3 class="font-semibold text-lg">Kop Surat</h3>
                    @if($kop && $kop->gambar)
                        <img src="{{ asset('storage/' . $kop->gambar) }}" class="mt-2 max-w-full h-auto rounded shadow">
                    @else
                        <p class="text-gray-500 italic">Belum ada gambar kop Surat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
