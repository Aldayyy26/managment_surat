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
                                <th class="border border-gray-300 px-4 py-2 text-left">Isi Surat</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengajuanSurats as $index => $surat)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->template->judul }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->created_at->format('Y-m-d') }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $surat->user->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ Str::limit(implode(', ', json_decode($surat->konten, true)), 50, '...') }} 
                                        <button class="text-blue-500 underline" onclick="showSuratDetail(`{{ json_encode(json_decode($surat->konten, true)) }}`)">Lihat Detail</button>
                                    </td>

                                    <td class="border border-gray-300 px-4 py-2 flex space-x-2">
                                        <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600"
                                            onclick="openSignatureModal(this)" data-id="{{ $surat->id }}">
                                            ✔ Setujui
                                        </button>
                                        <button class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"
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
        </div>
    </div>

    <!-- Modal for Viewing Surat Detail -->
    <div id="suratDetailModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Detail Surat</h2>
            <p id="suratContent" class="text-gray-700"></p>
            <button class="bg-gray-500 text-white px-4 py-2 rounded-md mt-4" onclick="closeSuratDetailModal()">Tutup</button>
        </div>
    </div>

    <!-- Modal for Signature -->
    <div id="signatureModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Tanda Tangan</h2>
            <canvas id="signatureCanvas" class="border border-gray-300 w-full h-48"></canvas>
            <div class="mt-4 flex justify-between">
                <button class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                    onclick="closeSignatureModal()">Batal</button>
                <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600"
                    onclick="saveSignature()">Simpan</button>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad;
    let canvas;
    let selectedSuratId = null;

    function showSuratDetail(content) {
    let parsedContent = JSON.parse(content);
    let formattedContent = Object.entries(parsedContent)
        .map(([key, value]) => `<b>${key}:</b> ${value}`)
        .join("<br>");
    
    document.getElementById('suratContent').innerHTML = formattedContent;
    document.getElementById('suratDetailModal').classList.remove('hidden');
}


    function closeSuratDetailModal() {
        document.getElementById('suratDetailModal').classList.add('hidden');
    }

    function openSignatureModal(button) {
        selectedSuratId = button.getAttribute("data-id");

        const modal = document.getElementById('signatureModal');
        modal.classList.remove('hidden');

        canvas = document.getElementById('signatureCanvas');
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
        signaturePad.clear();
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
            body: JSON.stringify({ signature: signatureData }) 
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
