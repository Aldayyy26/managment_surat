<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tanda Tangan Kaprodi') }}
        </h2>
    </x-slot>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        #signatureCanvas {
            touch-action: none; /* Bisa ditulis di layar sentuh */
        }
    </style>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded-lg shadow-md">
            {{-- Tampilkan tanda tangan lama jika ada --}}
            @php
                $existingSignature = \Illuminate\Support\Facades\Storage::disk('public')->exists(App\Http\Controllers\SignatureController::getSignaturePath());
            @endphp

            @if($existingSignature)
                <div class="mb-4 text-center">
                    <p class="text-gray-700 mb-2">Tanda tangan sebelumnya:</p>
                    <img src="{{ asset('storage/' . App\Http\Controllers\SignatureController::getSignaturePath()) }}"
                         alt="Tanda tangan lama" class="border border-gray-300 mx-auto mb-4 max-h-40">
                </div>
            @endif

            {{-- Canvas tanda tangan baru --}}
            <canvas id="signatureCanvas" class="border border-gray-300 w-full h-48"></canvas>

            <div class="flex justify-between mt-4">
                <button onclick="signaturePad.clear()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Hapus</button>
                <button onclick="saveSignature()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Simpan</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>
    <script>
        let signaturePad;

        window.onload = function () {
            const canvas = document.getElementById('signatureCanvas');
            resizeCanvas();

            signaturePad = new SignaturePad(canvas);

            window.addEventListener("resize", resizeCanvas);

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }
        }

        function saveSignature() {
            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda tangan kosong',
                    text: 'Silakan buat tanda tangan terlebih dahulu.',
                });
                return;
            }

            const signatureData = signaturePad.toDataURL('image/png');

            fetch("{{ route('signature.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ signature: signatureData })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('signature.index') }}";
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyimpan tanda tangan!',
                    text: 'Silakan coba lagi.',
                });
                console.error(err);
            });
        }
    </script>
</x-app-layout>
