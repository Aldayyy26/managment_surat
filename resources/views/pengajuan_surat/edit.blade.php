<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pengajuan Surat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('pengajuan-surat.update', $pengajuanSurat->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Template Surat (Non-Editable) --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Template Surat</label>
                        <select id="templateSurat" name="template_id" class="w-full p-2 border rounded-lg bg-gray-200" disabled>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ $pengajuanSurat->template_id == $template->id ? 'selected' : '' }}>
                                    {{ $template->judul }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="template_id" value="{{ $pengajuanSurat->template_id }}">
                    </div>

                    {{-- Dynamic Fields --}}
                    <div id="dynamicFields"></div>

                    {{-- Tombol Submit --}}
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('pengajuan-surat.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Load Data --}}
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var templateId = "{{ $pengajuanSurat->template_id }}";
        var dynamicFields = document.getElementById('dynamicFields');
        var existingData = @json(json_decode($pengajuanSurat->konten, true));

        function loadTemplateFields(templateId) {
            dynamicFields.innerHTML = '';

            if (templateId) {
                fetch(`/get-template-fields/${templateId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            dynamicFields.innerHTML = `<p class="text-danger">${data.error}</p>`;
                            return;
                        }

                        data.forEach(field => {
                            let formElement = '';
                            let value = existingData[field.label] || '';

                            switch(field.type) {
                                case 'text':
                                case 'email':
                                case 'number':
                                case 'date':
                                    formElement = `<div class="mb-3">
                                        <label class="form-label fw-bold">${field.label}:</label>
                                        <input type="${field.type}" name="konten[${field.label}]" class="form-control" value="${value}" required>
                                    </div>`;
                                    break;
                                case 'textarea':
                                    formElement = `<div class="mb-3">
                                        <label class="form-label fw-bold">${field.label}:</label>
                                        <textarea name="konten[${field.label}]" class="form-control" required>${value}</textarea>
                                    </div>`;
                                    break;
                                case 'select':
                                    formElement = `<div class="mb-3">
                                        <label class="form-label fw-bold">${field.label}:</label>
                                        <select name="konten[${field.label}]" class="form-select" required>
                                            ${field.options.map(option => 
                                                `<option value="${option}" ${option == value ? 'selected' : ''}>${option}</option>`
                                            ).join('')}
                                        </select>
                                    </div>`;
                                    break;
                                case 'checkbox':
                                    let checked = value ? 'checked' : '';
                                    formElement = `<div class="form-check mb-3">
                                        <input type="checkbox" name="konten[${field.label}]" class="form-check-input" ${checked}>
                                        <label class="form-check-label">${field.label}</label>
                                    </div>`;
                                    break;
                                case 'radio':
                                    formElement = `<div class="mb-3">
                                        <label class="form-label fw-bold">${field.label}:</label>
                                        <div>
                                            ${field.options.map(option => `
                                                <div class="form-check">
                                                    <input type="radio" name="konten[${field.label}]" value="${option}" class="form-check-input" ${option == value ? 'checked' : ''}>
                                                    <label class="form-check-label">${option}</label>
                                                </div>`
                                            ).join('')}
                                        </div>
                                    </div>`;
                                    break;
                                default:
                                    console.warn("Unknown field type:", field.type);
                            }
                            dynamicFields.innerHTML += formElement;
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching template:', error);
                        dynamicFields.innerHTML = `<p class="text-danger">Gagal mengambil data template. Coba lagi.</p>`;
                    });
            }
        }

        // Load fields on page load
        loadTemplateFields(templateId);
    });
    </script>

</x-app-layout>
