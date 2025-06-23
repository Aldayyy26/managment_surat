<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('List Pengajuan Surat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Form filter pengaju --}}
                    <form method="GET" action="{{ route('admin.report') }}" class="mb-6">
                        <div class="flex flex-col sm:flex-row sm:justify-end sm:items-end gap-4">
                            <div>
                                <label for="pengaju" class="block text-gray-700 font-semibold mb-1">Pengaju:</label>
                                <input type="text" name="pengaju" id="pengaju" value="{{ request('pengaju') }}"
                                    placeholder="Nama pengaju"
                                    class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-52" />
                            </div>

                            <div>
                                <label for="nama_surat" class="block text-gray-700 font-semibold mb-1">Judul Surat:</label>
                                <input type="text" name="nama_surat" id="nama_surat" value="{{ request('nama_surat') }}"
                                    placeholder="Judul surat"
                                    class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-52" />
                            </div>

                            <div>
                                <label for="status" class="block text-gray-700 font-semibold mb-1">Status:</label>
                                <select name="status" id="status"
                                    class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-40">
                                    <option value="">Semua</option>
                                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            <div>
                                <label for="tanggal" class="block text-gray-700 font-semibold mb-1">Tanggal:</label>
                                <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}"
                                    class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-48" />
                            </div>

                            <div>
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mt-1 sm:mt-6">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-4 rounded-md mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    {{-- Table wrapper untuk responsif --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700">
                                    <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Judul Surat</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Pengaju</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Isi Surat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuanSurats as $index => $surat)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->created_at->format('d-m-Y') }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->template->nama_surat }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ ucfirst($surat->status) }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->user->name ?? '-' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ Str::limit(implode(', ', json_decode($surat->konten, true)), 50, '...') }}
                                        <button class="text-blue-500 underline text-sm"
                                            onclick="previewPdf('{{ route('pengajuan_surat.preview', $surat->id) }}')">
                                            Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data pengajuan surat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $pengajuanSurats->withQueryString()->links() }}
                    </div>

                    <!-- Modal Preview PDF -->
                    <div id="pdfModal"
                        class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl h-[80vh] flex flex-col">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h2 class="text-xl font-bold">Preview Surat</h2>
                                <button onclick="closePdfModal()" class="text-red-600 font-bold text-lg">&times;</button>
                            </div>
                            <iframe id="pdfFrame" class="flex-1 w-full" frameborder="0"></iframe>
                        </div>
                    </div>

                    <!-- CSRF Token -->
                    <meta name="csrf-token" content="{{ csrf_token() }}">

                    <script>
                        function previewPdf(url) {
                            fetch(url)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.url) {
                                        document.getElementById('pdfFrame').src = data.url;
                                        document.getElementById('pdfModal').classList.remove('hidden');
                                    } else {
                                        alert("Gagal memuat file PDF.");
                                    }
                                })
                                .catch(() => {
                                    alert("Gagal memuat preview surat.");
                                });
                        }

                        function closePdfModal() {
                            document.getElementById('pdfModal').classList.add('hidden');
                            document.getElementById('pdfFrame').src = '';
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>