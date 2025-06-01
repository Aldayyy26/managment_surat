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
                <select name="required_placeholders[{{ $placeholder }}][type]" class="w-full border rounded px-2 py-1">
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
        </div>
    @endforeach

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Simpan Placeholder Wajib
    </button>
</form>

<script>
    document.querySelectorAll('select[name^="required_placeholders"]').forEach(function(select) {
        select.addEventListener('change', function() {
            const placeholder = this.name.match(/\[(.*?)\]/)[1];
            const optionsInput = document.getElementById('options-' + placeholder);
            if (this.value === 'select') {
                optionsInput.classList.remove('hidden');
            } else {
                optionsInput.classList.add('hidden');
            }
        });
    });
</script>
