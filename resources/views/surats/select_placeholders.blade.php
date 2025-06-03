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

                <p>Silakan pilih placeholder yang bisa diisi dan tentukan label serta tipe data:</p>

                @foreach ($placeholders as $placeholder)
                    <div class="mb-4 border p-3 rounded">
                        <h4 class="font-semibold mb-2">Placeholder: <code>{{ $placeholder }}</code></h4>

                        <label>
                            <input
                                type="checkbox"
                                name="required_placeholders[{{ $placeholder }}][can_input]"
                                value="1"
                                {{ old("required_placeholders.$placeholder.can_input") ? 'checked' : '' }}
                            >
                            Bisa diajukan user
                        </label>

                        <label class="ml-4">
                            <input
                                type="checkbox"
                                name="required_placeholders[{{ $placeholder }}][nullable]"
                                value="1"
                                {{ old("required_placeholders.$placeholder.nullable") ? 'checked' : '' }}
                            >
                            Boleh kosong (nullable)
                        </label>

                        <div class="mt-2">
                            <label>Label untuk form user:</label>
                            <input
                                type="text"
                                name="required_placeholders[{{ $placeholder }}][label]"
                                class="w-full border rounded px-2 py-1"
                                placeholder="Contoh: Nama Lengkap"
                                value="{{ old("required_placeholders.$placeholder.label") }}"
                            >
                        </div>

                        <div class="mt-2">
                            <label>Tipe data:</label>
                            <select
                                name="required_placeholders[{{ $placeholder }}][type]"
                                class="w-full border rounded px-2 py-1 type-select"
                                data-placeholder="{{ $placeholder }}"
                            >
                                <option value="text" {{ old("required_placeholders.$placeholder.type") == 'text' ? 'selected' : '' }}>Teks</option>
                                <option value="number" {{ old("required_placeholders.$placeholder.type") == 'number' ? 'selected' : '' }}>Nomor</option>
                                <option value="date" {{ old("required_placeholders.$placeholder.type") == 'date' ? 'selected' : '' }}>Tanggal</option>
                                <option value="textarea" {{ old("required_placeholders.$placeholder.type") == 'textarea' ? 'selected' : '' }}>Paragraf</option>
                                <option value="select" {{ old("required_placeholders.$placeholder.type") == 'select' ? 'selected' : '' }}>Pilihan</option>
                            </select>
                        </div>

                        <div
                            class="mt-2 {{ old("required_placeholders.$placeholder.type") == 'select' ? '' : 'hidden' }}"
                            id="options-{{ $placeholder }}"
                        >
                            <label>Opsi (pisahkan dengan koma):</label>
                            <input
                                type="text"
                                name="required_placeholders[{{ $placeholder }}][options]"
                                class="w-full border rounded px-2 py-1"
                                placeholder="Contoh: opsi1, opsi2, opsi3"
                                value="{{ old("required_placeholders.$placeholder.options") }}"
                            >
                        </div>
                    </div>
                @endforeach

                <button
                    type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
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

            if (this.value === 'select') {
                optionsInput.classList.remove('hidden');
            } else {
                optionsInput.classList.add('hidden');
            }
        });
    });
</script>
