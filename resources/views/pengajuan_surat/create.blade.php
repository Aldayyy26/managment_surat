@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm p-4">
        <h3 class="text-center mb-4">Ajukan Pembuatan Surat</h3>
        
        <form action="{{ route('pengajuan-surat.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="templateSurat" class="form-label fw-bold">Pilih Template Surat:</label>
                <select id="templateSurat" name="template_id" class="form-select" required>
                    <option value="">-- Pilih Template --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->judul }}</option>
                    @endforeach
                </select>
            </div>
            
            <div id="dynamicFields"></div>
            
            <button type="submit" class="btn btn-primary w-100 mt-3">Ajukan Surat</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('templateSurat').addEventListener('change', function() {
        var templateId = this.value;
        var dynamicFields = document.getElementById('dynamicFields');
        dynamicFields.innerHTML = '';

        if (templateId) {
            fetch(`/get-template-fields/${templateId}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Response JSON:", data); // Debugging

                    if (data.error) {
                        dynamicFields.innerHTML = `<p class="text-danger">${data.error}</p>`;
                        return;
                    }

                    data.forEach(field => {
                        let formElement = '';
                        switch(field.type) {
                            case 'text':
                            case 'email':
                            case 'number':
                            case 'date':
                                formElement = `<div class="mb-3">
                                    <label class="form-label fw-bold">${field.label}:</label>
                                    <input type="${field.type}" name="konten[${field.label}]" class="form-control" required>
                                </div>`;
                                break;
                            case 'textarea':
                                formElement = `<div class="mb-3">
                                    <label class="form-label fw-bold">${field.label}:</label>
                                    <textarea name="konten[${field.label}]" class="form-control" required></textarea>
                                </div>`;
                                break;
                            case 'select':
                                formElement = `<div class="mb-3">
                                    <label class="form-label fw-bold">${field.label}:</label>
                                    <select name="konten[${field.label}]" class="form-select" required>
                                        ${field.options.map(option => `<option value="${option}">${option}</option>`).join('')}
                                    </select>
                                </div>`;
                                break;
                            case 'checkbox':
                                formElement = `<div class="form-check mb-3">
                                    <input type="checkbox" name="konten[${field.label}]" class="form-check-input">
                                    <label class="form-check-label">${field.label}</label>
                                </div>`;
                                break;
                            case 'radio':
                                formElement = `<div class="mb-3">
                                    <label class="form-label fw-bold">${field.label}:</label>
                                    <div>
                                        ${field.options.map(option => `
                                            <div class="form-check">
                                                <input type="radio" name="konten[${field.label}]" value="${option}" class="form-check-input">
                                                <label class="form-check-label">${option}</label>
                                            </div>`).join('')}
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
    });
});
</script>
@endsection 
