<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tanda Tangan Kaprodi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded-lg shadow-md text-center">
            @if ($signatureUrl)
                <img src="{{ $signatureUrl }}" alt="Tanda Tangan Kaprodi" class="mx-auto mb-4 border border-gray-300">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus Tanda Tangan</button>
                </form>
            @else
                <p class="text-gray-600">Belum ada tanda tangan yang disimpan.</p>
            @endif

            <a href="{{ route('signature.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                {{ $signatureUrl ? 'Edit Tanda Tangan' : 'Buat Tanda Tangan Baru' }}
            </a>
        </div>
    </div>

    <script>
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function (e) {
                e.preventDefault();

                if (!confirm("Yakin ingin menghapus tanda tangan?")) return;

                fetch("{{ route('signature.destroy') }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(err => {
                    alert("Gagal menghapus tanda tangan.");
                    console.error(err);
                });
            });
        }
    </script>
</x-app-layout>
