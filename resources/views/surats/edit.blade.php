<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('surats.update', $surat) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Judul Surat</label>
                            <input type="text" name="judul" class="form-input mt-1 block w-full" value="{{ old('judul', $surat->judul) }}" required>
                        </div>

                        <div id="konten-wrapper" class="mb-4">
                            <label class="block text-gray-700 font-medium">Konten Surat</label>

                            @php
                                $kontenList = old('konten', json_decode($surat->konten, true) ?? []);
                            @endphp

                            @foreach ($kontenList as $index => $item)
                                <div class="flex items-center space-x-2 mb-2 konten-item">
                                    <input type="text" name="konten[{{ $index }}][label]" class="form-input mt-1 block w-full"
                                           value="{{ old("konten.$index.label", $item['label'] ?? '') }}" required placeholder="Nama Konten">
                                    <input list="input-types" name="konten[{{ $index }}][type]" class="form-input w-32" 
                                           value="{{ old("konten.$index.type", $item['type'] ?? '') }}" placeholder="Pilih Type">
                                    <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md remove-konten">-</button>
                                </div>
                            @endforeach
                            
                            <button type="button" class="bg-green-500 text-white px-4 py-2 rounded-md add-konten">Tambah</button>
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<datalist id="input-types">
    <option value="text">
    <option value="date">
    <option value="number">
    <option value="email">
    <option value="password">
    <option value="tel">
    <option value="url">
    <option value="color">
    <option value="time">
    <option value="datetime-local">
    <option value="month">
    <option value="week">
    <option value="range">
    <option value="file">
    <option value="checkbox">
    <option value="radio">
</datalist>

<script>
    let kontenIndex = {{ count($kontenList) }};

    document.querySelector('.add-konten').addEventListener('click', function () {
        const wrapper = document.getElementById('konten-wrapper');
        const newField = document.createElement('div');
        newField.classList.add('flex', 'items-center', 'space-x-2', 'mb-2', 'konten-item');

        newField.innerHTML = `
            <input type="text" name="konten[${kontenIndex}][label]" class="form-input mt-1 block w-full" required placeholder="Nama Konten">
            <input list="input-types" name="konten[${kontenIndex}][type]" class="form-input w-32" placeholder="Pilih Type">
            <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md remove-konten">-</button>
        `;

        wrapper.insertBefore(newField, wrapper.querySelector('.add-konten'));
        kontenIndex++;

        newField.querySelector('.remove-konten').addEventListener('click', function () {
            newField.remove();
        });
    });

    document.querySelectorAll('.remove-konten').forEach(button => {
        button.addEventListener('click', function () {
            this.parentElement.remove();
        });
    });
</script>
