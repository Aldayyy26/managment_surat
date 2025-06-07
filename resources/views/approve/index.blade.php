<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Approval Surat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700">
                                    <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Nama Surat</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Tanggal</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Pengaju</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Isi Surat</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuanSurats as $index => $surat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $surat->template->nama_surat }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $surat->created_at->format('Y-m-d') }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $surat->user->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ Str::limit(implode(', ', json_decode($surat->konten, true)), 50, '...') }}
                                            <button class="text-blue-500 underline text-sm" onclick="showSuratDetail(`{{ json_encode(json_decode($surat->konten, true)) }}`)">
                                                Lihat Detail
                                            </button>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 flex space-x-2">
                                            <button
                                                class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600"
                                                title="Setujui"
                                                onclick="approveSurat({{ $surat->id }})">
                                                ✔ Setujui
                                            </button>
                                            <button
                                                class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600"
                                                title="Tolak"
                                                onclick="rejectSurat({{ $surat->id }})">
                                                ✖ Tolak
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Detail Surat -->
                <div id="suratDetailModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                        <h2 class="text-xl font-bold mb-4">Detail Surat</h2>
                        <p id="suratContent" class="text-gray-700"></p>
                        <button class="bg-gray-500 text-white px-4 py-2 rounded-md mt-4" onclick="closeSuratDetailModal()">Tutup</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showSuratDetail(content) {
        const parsedContent = JSON.parse(content);
        const formatted = Object.entries(parsedContent)
            .map(([key, val]) => `<strong>${key}</strong>: ${val}`)
            .join("<br>");
        document.getElementById('suratContent').innerHTML = formatted;
        document.getElementById('suratDetailModal').classList.remove('hidden');
    }

    function closeSuratDetailModal() {
        document.getElementById('suratDetailModal').classList.add('hidden');
    }

    function approveSurat(suratId) {
        Swal.fire({
            title: 'Setujui Surat?',
            text: 'Apakah Anda yakin ingin menyetujui surat ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/pengajuan_surat/${suratId}/diterima`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    document.querySelector(`button[onclick="approveSurat(${suratId})"]`).closest('tr').remove();
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyetujui surat.'
                    });
                });
            }
        });
    }

    function rejectSurat(suratId) {
        Swal.fire({
            title: 'Tolak Surat?',
            text: 'Apakah Anda yakin ingin menolak surat ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Tolak'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/pengajuan_surat/${suratId}/ditolak`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditolak!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    document.querySelector(`button[onclick="rejectSurat(${suratId})"]`).closest('tr').remove();
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menolak surat.'
                    });
                });
            }
        });
    }
</script>   
