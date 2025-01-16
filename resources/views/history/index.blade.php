<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Surat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="border border-gray-300 px-4 py-2 text-left">#</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Nomor Surat</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Keterangan</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="riwayatTable">
                            <!-- Riwayat surat akan muncul di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Contoh data riwayat surat, ganti dengan data asli Anda
        let riwayatSurat = [
            { id: 1, nomorSurat: '123/2023', status: 'Diterima', keterangan: 'Disetujui oleh admin', tanggal: '01 Januari 2023, 14:30' },
            { id: 2, nomorSurat: '124/2023', status: 'Ditolak', keterangan: 'Ditolak karena detail tidak valid', tanggal: '02 Januari 2023, 15:45' },
        ];

        function perbaruiTabel() {
            const tableBody = document.getElementById("riwayatTable");
            tableBody.innerHTML = "";

            riwayatSurat.forEach((riwayat) => {
                const row = `
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2">${riwayat.id}</td>
                        <td class="border border-gray-300 px-4 py-2">${riwayat.nomorSurat}</td>
                        <td class="border border-gray-300 px-4 py-2">${riwayat.status}</td>
                        <td class="border border-gray-300 px-4 py-2">${riwayat.keterangan}</td>
                        <td class="border border-gray-300 px-4 py-2">${riwayat.tanggal}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            ${riwayat.status === 'Diterima' ? 
                                `<button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400">
                                    Unduh Surat
                                </button>` : 
                                '<span class="text-gray-500">Tidak Dapat Diunduh</span>'
                            }
                        </td>
                    </tr>`;
                tableBody.insertAdjacentHTML("beforeend", row);
            });
        }

        // Panggil perbaruiTabel untuk mengisi tabel riwayat surat saat halaman dimuat
        perbaruiTabel();
    </script>
</x-app-layout>
