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
                    <table class="min-w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Nama Surat</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Pengaju</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($pengajuanSurats as $index => $surat)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                                    <!-- Menampilkan nama surat dari TemplateSurat -->
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->template->judul }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->created_at->format('Y-m-d') }}</td>
                                    <!-- Menampilkan nama pengguna yang mengajukan -->
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->user->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2 flex space-x-2">
                                        <button
                                            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center"
                                            onclick="openSignatureModal(this)" data-id="{{ $surat->id }}">
                                            ✔ Setujui
                                        </button>
                                        <button
                                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 flex items-center"
                                            onclick="rejectSurat({{ $surat->id }})">
                                            ✖ Tolak
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Modal untuk tanda tangan -->
                <div id="signatureModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                        <h2 class="text-lg font-semibold mb-4">Tanda Tangan</h2>
                        <canvas id="signatureCanvas" class="border border-gray-300 w-full h-40"></canvas>
                        <div class="flex justify-between mt-4">
                            <button onclick="signaturePad.clear()" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">Hapus</button>
                            <button onclick="closeSignatureModal()" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Batal</button>
                            <button onclick="saveSignature()" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Simpan</button>
                        </div>
                    </div>
                </div>
</x-app-layout>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>
    <script>
    let signaturePad;
    let selectedSuratId = null;

    function openSignatureModal(button) {
        selectedSuratId = button.getAttribute("data-id");
        const modal = document.getElementById('signatureModal');
        modal.classList.remove('hidden'); // Menampilkan modal

        // Ambil canvas dan inisialisasi SignaturePad
        const canvas = document.getElementById('signatureCanvas');
        canvas.width = canvas.offsetWidth; // Pastikan ukuran sesuai dengan modal
        canvas.height = 150; // Set tinggi tetap

        const context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height); // Bersihkan canvas
        
        if (!signaturePad) {
            signaturePad = new SignaturePad(canvas, {
                penColor: "#000000",
                backgroundColor: "#ffffff",
                minWidth: 1,
                maxWidth: 3
            });
        } else {
            signaturePad.clear();
        }
    }

    function closeSignatureModal() {
        document.getElementById('signatureModal').classList.add('hidden');
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    function saveSignature() {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('Tanda tangan tidak boleh kosong!');
            return;
        }
        const signatureData = signaturePad.toDataURL("image/png");

        fetch(`/pengajuan-surat/${selectedSuratId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ signature: signatureData })  // Kirim tanda tangan sebagai base64
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            closeSignatureModal();
            location.reload();
        })
        .catch(error => {
            console.error("Error approving surat:", error);
            alert("Terjadi kesalahan!");
        });
    }

    function rejectSurat(suratId) {
            if (confirm('Apakah Anda yakin ingin menolak surat ini?')) {
                fetch(`/pengajuan-surat/${suratId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error("Error rejecting surat:", error);
                    alert("Terjadi kesalahan!");
                });
            }
        }
</script>