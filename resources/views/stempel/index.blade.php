<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Stempel') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tombol Tambah --}}
            @if ($stempels->count() == 0)
                <a href="{{ route('stempels.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Tambah Stempel
                </a>
            @endif

            {{-- Tabel Daftar --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                @forelse ($stempels as $stempel)
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $stempel->nama }}</h3>
                            <img src="{{ asset('storage/' . $stempel->gambar) }}" alt="Stempel" class="w-40 mt-2">
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('stempels.edit', $stempel->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Edit
                            </a>

                            <form action="{{ route('stempels.destroy', $stempel->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p>Tidak ada stempel tersedia.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
