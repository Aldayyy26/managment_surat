<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template Surat') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 text-green-600">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('surats.update', $template->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 shadow rounded">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block font-medium">Nomor Jenis Surat</label>
                    <input type="text" name="no_jenis_surat" value="{{ old('no_jenis_surat', $template->no_jenis_surat) }}" class="w-full border rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Nama Surat</label>
                    <input type="text" name="nama_surat" value="{{ old('nama_surat', $template->nama_surat) }}" class="w-full border rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">Jenis Pengguna</label>
                    <select name="user_type" class="w-full border rounded px-4 py-2" required>
                        <option value="">-- Pilih --</option>
                        <option value="mahasiswa" {{ old('user_type', $template->user_type) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('user_type', $template->user_type) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block font-medium">File Surat (.docx)</label>
                    @if ($template->file_path)
                    <p class="text-sm mt-2 text-gray-800">File saat ini: <strong>{{ basename($template->file_path) }}</strong></p>
                    @else
                    <p class="text-sm text-red-600">Tidak ada file diunggah.</p>
                    @endif
                    <p class="text-sm text-gray-500 mt-1 italic">* File tidak dapat diubah dari halaman edit.</p>
                </div>


                <hr class="my-6 border-gray-300">

                <h3 class="text-lg font-semibold mb-4">Placeholder Wajib & Konfigurasi</h3>
                <p class="mb-4 text-sm text-gray-700">Tentukan placeholder mana saja yang wajib diisi oleh pengguna, termasuk label dan tipe datanya.</p>

                @foreach ($placeholders as $placeholder)
                @php
                $trimmed = trim($placeholder);
                $config = $existingPlaceholders[$trimmed] ?? [
                'required' => false,
                'can_input' => true,
                'nullable' => false,
                'label' => '',
                'type' => 'text',
                'options' => '',
                ];
                @endphp

                <div class="mb-4 border p-3 rounded">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="required_placeholders[{{ $trimmed }}][required]" value="1"
                            {{ old("required_placeholders.$trimmed.required", $config['required']) ? 'checked' : '' }}>
                        <span class="ml-2 font-semibold">{{ $trimmed }}</span>
                    </label>

                    <div class="mt-2">
                        <label>
                            <input type="checkbox" name="required_placeholders[{{ $trimmed }}][can_input]" value="1"
                                {{ old("required_placeholders.$trimmed.can_input", $config['can_input']) ? 'checked' : '' }}>
                            Bisa diajukan user
                        </label>

                        <label class="ml-4">
                            <input type="checkbox" name="required_placeholders[{{ $trimmed }}][nullable]" value="1"
                                {{ old("required_placeholders.$trimmed.nullable", $config['nullable']) ? 'checked' : '' }}>
                            Boleh kosong (nullable)
                        </label>
                    </div>

                    <div class="mt-2">
                        <label>Label untuk form user:</label>
                        <input type="text" name="required_placeholders[{{ $trimmed }}][label]" class="w-full border rounded px-2 py-1"
                            value="{{ old("required_placeholders.$trimmed.label", $config['label']) }}"
                            placeholder="Contoh: Nama Lengkap">
                    </div>

                    <div class="mt-2">
                        <label>Tipe data:</label>
                        <select name="required_placeholders[{{ $trimmed }}][type]" class="w-full border rounded px-2 py-1 type-select"
                            data-placeholder="{{ $trimmed }}">
                            <option value="text" {{ old("required_placeholders.$trimmed.type", $config['type']) == 'text' ? 'selected' : '' }}>Teks</option>
                            <option value="number" {{ old("required_placeholders.$trimmed.type", $config['type']) == 'number' ? 'selected' : '' }}>Nomor</option>
                            <option value="date" {{ old("required_placeholders.$trimmed.type", $config['type']) == 'date' ? 'selected' : '' }}>Tanggal</option>
                            <option value="textarea" {{ old("required_placeholders.$trimmed.type", $config['type']) == 'textarea' ? 'selected' : '' }}>Paragraf</option>
                            <option value="select" {{ old("required_placeholders.$trimmed.type", $config['type']) == 'select' ? 'selected' : '' }}>Pilihan</option>
                        </select>
                    </div>

                    <div class="mt-2 {{ $config['type'] == 'select' ? '' : 'hidden' }}" id="options-{{ $trimmed }}">
                        <label>Opsi (pisahkan dengan koma):</label>
                        <input type="text" name="required_placeholders[{{ $trimmed }}][options]" class="w-full border rounded px-2 py-1"
                            value="{{ old("required_placeholders.$trimmed.options", is_array($config['options']) ? implode(',', $config['options']) : $config['options']) }}"
                            placeholder="Contoh: opsi1, opsi2, opsi3">
                    </div>

                    <div class="mt-2 {{ $config['type'] == 'date' ? '' : 'hidden' }}" id="date-preview-{{ $trimmed }}">
                        <label>Contoh Input Tanggal:</label>
                        <input type="date" class="w-full border rounded px-2 py-1 date-input">
                    </div>
                </div>
                @endforeach



                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.type-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const placeholder = this.dataset.placeholder;
                const optionsDiv = document.getElementById('options-' + placeholder);
                const dateDiv = document.getElementById('date-preview-' + placeholder);

                if (this.value === 'select') {
                    optionsDiv.classList.remove('hidden');
                } else {
                    optionsDiv.classList.add('hidden');
                }

                if (this.value === 'date') {
                    dateDiv.classList.remove('hidden');
                    const dateInput = dateDiv.querySelector('.date-input');
                    const today = new Date().toISOString().split('T')[0];
                    dateInput.setAttribute('min', today);
                } else {
                    dateDiv.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>