<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Template Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('surats.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Judul Surat</label>
                            <input type="text" name="judul" class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Lampiran</label>
                            <input type="text" name="lampiran" class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Perihal</label>
                            <input type="text" name="perihal" class="form-input mt-1 block w-full" placeholder="Boleh dikosongkan jika tidak diperlukan">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Kepada Yth</label>
                            <input type="text" name="kepada_yth" class="form-input mt-1 block w-full" placeholder="Boleh dikosongkan jika tidak diperlukan">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Pembuka</label>
                            <input type="text" name="pembuka" class="form-input mt-1 block w-full" placeholder="Boleh dikosongkan jika tidak diperlukan">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Teks Atas</label>
                            <input type="text" name="teks_atas" class="form-input mt-1 block w-full" placeholder="Boleh dikosongkan jika tidak diperlukan">
                        </div>

                        <div id="konten-wrapper" class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Konten Surat</label>
                            <button type="button" class="bg-green-500 text-white px-4 py-2 rounded-md add-konten">Tambah Konten</button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Teks Bawah</label>
                            <input type="text" name="teks_bawah" class="form-input mt-1 block w-full" placeholder="Boleh dikosongkan jika tidak diperlukan">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium">Penutup</label>
                            <input type="text" name="penutup" class="form-input mt-1 block w-full" placeholder="Boleh dikosongkan jika tidak diperlukan">
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let kontenIndex = 0;

        document.querySelector('.add-konten').addEventListener('click', function () {
            const wrapper = document.getElementById('konten-wrapper');
            const newField = document.createElement('div');
            newField.classList.add('flex', 'items-center', 'space-x-2', 'mb-2');

            newField.innerHTML = `
            <input type="text" name="konten[${kontenIndex}][label]" class="form-input mt-1 block w-full" placeholder="Label Konten">
            <select name="konten[${kontenIndex}][type]" class="form-input w-40">
                <option value="">Pilih Tipe</option>
                <option value="text">Text</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
                <option value="email">Email</option>
                <option value="textarea">Textarea</option>
                <option value="checkbox">Checkbox</option>
                <option value="radio">Radio</option>
                <option value="select">Select</option>
            </select>
            <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md remove-konten">-</button>
            `;


            wrapper.appendChild(newField);
            kontenIndex++;

            newField.querySelector('.remove-konten').addEventListener('click', function () {
                newField.remove();
            });
        });
    </script>
</x-app-layout>
