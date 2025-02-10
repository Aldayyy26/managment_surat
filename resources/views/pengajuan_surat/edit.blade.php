<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            ✏️ Edit Pengajuan Surat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-white shadow-lg rounded-lg p-8">
                <form method="POST" action="{{ route('pengajuan-surat.update', $pengajuanSurat->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Template Surat (Non-Editable) --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2 text-lg">📄 Template Surat</label>
                        <select id="templateSurat" name="template_id" class="w-full p-3 border rounded-lg bg-gray-200 text-gray-700 cursor-not-allowed" disabled>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ $pengajuanSurat->template_id == $template->id ? 'selected' : '' }}>
                                    {{ $template->judul }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="template_id" value="{{ $pengajuanSurat->template_id }}">
                    </div>

                    {{-- Dynamic Fields --}}
                    <div id="dynamicFields" class="space-y-4"></div>

                    {{-- Tombol Submit --}}
                    <div class="mt-6 flex space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('pengajuan-surat.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-600 transition">
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
                            dynamicFields.innerHTML = `<p class="text-red-600 font-semibold">${data.error}</p>`;
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
                                    formElement = `<div>
                                        <label class="block text-gray-700 font-medium">${field.label}</label>
                                        <input type="${field.type}" name="konten[${field.label}]" class="w-full p-3 border rounded-lg shadow-sm focus:ring focus:ring-blue-300" value="${value}" required>
                                    </div>`;
                                    break;
                                case 'textarea':
                                    formElement = `<div>
                                        <label class="block text-gray-700 font-medium">${field.label}</label>
                                        <textarea name="konten[${field.label}]" class="w-full p-3 border rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>${value}</textarea>
                                    </div>`;
                                    break;
                                case 'select':
                                    formElement = `<div>
                                        <label class="block text-gray-700 font-medium">${field.label}</label>
                                        <select name="konten[${field.label}]" class="w-full p-3 border rounded-lg shadow-sm focus:ring focus:ring-blue-300" required>
                                            ${field.options.map(option => 
                                                `<option value="${option}" ${option == value ? 'selected' : ''}>${option}</option>`
                                            ).join('')}
                                        </select>
                                    </div>`;
                                    break;
                                case 'checkbox':
                                    let checked = value ? 'checked' : '';
                                    formElement = `<div class="flex items-center space-x-2">
                                        <input type="checkbox" name="konten[${field.label}]" class="h-5 w-5 text-blue-600 rounded-lg focus:ring-blue-500" ${checked}>
                                        <label class="text-gray-700 font-medium">${field.label}</label>
                                    </div>`;
                                    break;
                                case 'radio':
                                    formElement = `<div>
                                        <label class="block text-gray-700 font-medium">${field.label}</label>
                                        <div class="flex flex-wrap gap-4">
                                            ${field.options.map(option => `
                                                <div class="flex items-center space-x-2">
                                                    <input type="radio" name="konten[${field.label}]" value="${option}" class="h-5 w-5 text-blue-600 focus:ring-blue-500" ${option == value ? 'checked' : ''}>
                                                    <label class="text-gray-700">${option}</label>
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
                        dynamicFields.innerHTML = `<p class="text-red-600 font-semibold">⚠️ Gagal mengambil data template. Coba lagi.</p>`;
                    });
            }
        }

        // Load fields on page load
        loadTemplateFields(templateId);
    });
    </script>

</x-app-layout>
