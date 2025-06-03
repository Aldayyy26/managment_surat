<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tanda Tangan Kaprodi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded-lg shadow-md">
            <canvas id="signatureCanvas" class="border border-gray-300 w-full h-40"></canvas>
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
            canvas.width = canvas.offsetWidth;
            canvas.height = 150;

            signaturePad = new SignaturePad(canvas);
        }

        function saveSignature() {
            if (signaturePad.isEmpty()) {
                alert("Tanda tangan tidak boleh kosong!");
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
                alert(data.message);
            })
            .catch(err => {
                alert("Gagal menyimpan tanda tangan.");
                console.error(err);
            });
        }
    </script>
</x-app-layout>
