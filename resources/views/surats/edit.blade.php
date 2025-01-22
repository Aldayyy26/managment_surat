<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Surat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('surats.update', $surat) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="judul" class="block text-gray-700 font-medium">Judul Surat</label>
                            <input type="text" name="judul" class="form-input mt-1 block w-full" id="judul" value="{{ $surat->judul }}" required>
                        </div>

                        <div id="konten-wrapper" class="mb-4">
                            <label for="konten" class="block text-gray-700 font-medium">Konten Surat</label>
                            @foreach ($surat->konten as $index => $item)
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" name="konten[]" class="form-input mt-1 block w-full" value="{{ $item }}" required>
                                    @if ($index === 0)
                                        <button type="button" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600" id="add-konten">+</button>
                                    @else
                                        <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 remove-konten">-</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.getElementById('add-konten')?.addEventListener('click', function () {
        const wrapper = document.getElementById('konten-wrapper');
        const newField = document.createElement('div');
        newField.classList.add('flex', 'items-center', 'space-x-2', 'mb-2');
        newField.innerHTML = `
            <input type="text" name="konten[]" class="form-input mt-1 block w-full" placeholder="Isi konten" required>
            <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 remove-konten">-</button>
        `;
        wrapper.appendChild(newField);

        newField.querySelector('.remove-konten').addEventListener('click', function () {
            this.parentElement.remove();
        });
    });

    document.querySelectorAll('.remove-konten').forEach(button => {
        button.addEventListener('click', function () {
            this.parentElement.remove();
        });
    });
</script>
