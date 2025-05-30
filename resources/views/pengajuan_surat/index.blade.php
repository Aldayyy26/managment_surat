<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Pengajuan Surat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Search Form -->
        <div class="flex justify-end mb-4">
            <form method="GET" action="{{ route('pengajuan-surat.index') }}" class="flex flex-col sm:flex-row gap-4">
                <input type="text" name="judul" placeholder="Cari judul surat" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ request('judul') }}">
                
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                    Cari
                </button>

                @if(request('judul') || request('status'))
                    <a href="{{ route('pengajuan-surat.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg font-semibold hover:bg-gray-500 transition duration-300">
                        Batal
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-4 rounded-md mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Judul Surat</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Tanggal</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengajuanSurats as $index => $surat)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $surat->template->judul }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ ucfirst($surat->status) }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $surat->created_at->format('d-m-Y') }}</td>
                                <td class="border border-gray-300 px-4 py-2 flex space-x-2">
                                    @if($surat->status == 'proses')
                                        <a href="{{ route('pengajuan-surat.edit', $surat->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                                            Edit
                                        </a>
                                    @endif
                                    @if($surat->status == 'diterima')
                                        <a href="{{ route('pengajuan-surat.download', $surat->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                            Download
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

</x-app-layout>
