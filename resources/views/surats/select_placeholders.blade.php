<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pilih Placeholder Wajib') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <form action="{{ route('surats.selectPlaceholders', $template->id) }}" method="POST" class="bg-white p-6 shadow rounded">
                @csrf

                <p>Silakan pilih placeholder yang wajib diisi dan tentukan label serta tipe data:</p>

                @foreach ($placeholders as $placeholder)
                    <div class="mb-4 border p-3 rounded">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="required_placeholders[{{ $placeholder }}][required]" value="1">
                            <span class="ml-2 font-semibold">{{ $placeholder }}</span>
                        </label>

                        <div class="mt-2">
                            <label>Label untuk form user:</label>
                            <input type="text" name="required_placeholders[{{ $placeholder }}][label]" class="w-full border rounded px-2 py-1" placeholder="Contoh: Nama Lengkap">
                        </div>

                        <div class="mt-2">
                            <label>Tipe data:</label>
                            <select name="required_placeholders[{{ $placeholder }}][type]" class="w-full border rounded px-2 py-1 type-select" data-placeholder="{{ $placeholder }}">
                                <option value="text">Teks</option>
                                <option value="number">Nomor</option>
                                <option value="date">Tanggal</option>
                                <option value="textarea">Paragraf</option>
                                <option value="select">Pilihan</option>
                            </select>
                        </div>

                        <div class="mt-2 hidden" id="options-{{ $placeholder }}">
                            <label>Opsi (pisahkan dengan koma):</label>
                            <input type="text" name="required_placeholders[{{ $placeholder }}][options]" class="w-full border rounded px-2 py-1" placeholder="Contoh: opsi1, opsi2, opsi3">
                        </div>

                        <!-- Input simulasi untuk tipe date (agar bisa uji behavior min) -->
                        <div class="mt-2 hidden" id="date-preview-{{ $placeholder }}">
                            <label>Contoh Input Tanggal:</label>
                            <input type="date" class="w-full border rounded px-2 py-1 date-input">
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Simpan Placeholder Wajib
                </button>
            </form>

        </div>
    </div>
</x-app-layout>

<script>
    document.querySelectorAll('.type-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const placeholder = this.dataset.placeholder;
            const optionsInput = document.getElementById('options-' + placeholder);
            const datePreview = document.getElementById('date-preview-' + placeholder);

            if (this.value === 'select') {
                optionsInput.classList.remove('hidden');
            } else {
                optionsInput.classList.add('hidden');
            }

            if (this.value === 'date') {
                // Tampilkan input date preview
                datePreview.classList.remove('hidden');

                // Set min date ke hari ini
                const dateInput = datePreview.querySelector('.date-input');
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
            } else {
                datePreview.classList.add('hidden');
            }
        });
    });
</script>
