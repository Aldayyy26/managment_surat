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
                                <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2">1</td>
                                <td class="border border-gray-300 px-4 py-2">Surat Undangan</td>
                                <td class="border border-gray-300 px-4 py-2">2024-11-30</td>
                                <td class="border border-gray-300 px-4 py-2 flex space-x-2">
                                    <button
                                        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center"
                                        onclick="approveSurat(1)">
                                        ✔ Setujui
                                    </button>
                                    <button
                                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 flex items-center"
                                        onclick="rejectSurat(1)">
                                        ✖ Tolak
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Signature -->
    <div id="signatureModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Tanda Tangan</h2>
            <canvas id="signatureCanvas" class="border border-gray-300 w-full h-48"></canvas>
            <div class="mt-4 flex justify-between">
                <button
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400"
                    onclick="closeSignatureModal()">Batal</button>
                <button
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400"
                    onclick="saveSignature()">Simpan</button>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad;
    let canvas;
    let context;
    let isCanvasResized = false; // Flag untuk memastikan resize hanya terjadi sekali

    function approveSurat(suratId) {
        console.log('Approve Surat Called:', suratId);

        // Tampilkan modal
        const modal = document.getElementById('signatureModal');
        if (!modal) {
            console.error("Modal element not found");
            return;
        }
        modal.classList.remove('hidden');

        // Ambil canvas dan context
        canvas = document.getElementById('signatureCanvas');
        if (!canvas) {
            console.error("Canvas element not found");
            return;
        }

        context = canvas.getContext('2d');
        if (!context) {
            console.error("Canvas context not found");
            return;
        }

        // Jika canvas belum diresize, lakukan resize hanya sekali
        if (!isCanvasResized) {
            resizeCanvas();
            isCanvasResized = true;
        }

        // Inisialisasi SignaturePad hanya jika belum ada
        if (!signaturePad) {
            signaturePad = new SignaturePad(canvas, {
                penColor: "#000000",
                backgroundColor: "#ffffff",
                minWidth: 1,
                maxWidth: 3,
                onEnd: function () {
                    console.log("SignaturePad drawing ended");
                }
            });
            console.log("SignaturePad initialized");
        } else {
            signaturePad.clear();
        }
    }

    function resizeCanvas() {
        const ratio = window.devicePixelRatio || 1; // Rasio layar
        const canvasWrapper = document.querySelector('#signatureModal .p-6'); // Cari elemen wrapper modal
        const canvasWidth = canvasWrapper.offsetWidth; // Ambil lebar dari wrapper
        const canvasHeight = 200; // Tentukan tinggi canvas sesuai kebutuhan

        // Sesuaikan ukuran canvas berdasarkan wrapper
        canvas.width = canvasWidth * ratio;
        canvas.height = canvasHeight * ratio;
        context.scale(ratio, ratio);

        // Bersihkan canvas dan set latar belakang putih
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = "#ffffff";
        context.fillRect(0, 0, canvas.width, canvas.height);

        console.log("Canvas resized and adjusted");
    }

    function rejectSurat(suratId) {
        if (confirm('Apakah Anda yakin ingin menolak surat ini?')) {
            alert(`Surat dengan ID ${suratId} ditolak.`);
        }
    }

    function closeSignatureModal() {
        const modal = document.getElementById('signatureModal');
        if (!modal) {
            console.error("Modal element not found");
            return;
        }
        modal.classList.add('hidden');

        if (signaturePad) {
            signaturePad.clear();
            console.log("Signature cleared");
        }
    }

    function saveSignature() {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('Tanda tangan tidak boleh kosong!');
            console.error("SignaturePad is empty");
            return;
        }

        const signatureData = signaturePad.toDataURL("image/png");
        console.log('Generated Signature Data:', signatureData);

        alert('Tanda tangan berhasil disimpan!');
        closeSignatureModal();
    }
</script>

