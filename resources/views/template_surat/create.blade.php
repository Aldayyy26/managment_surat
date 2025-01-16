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
                    <form method="POST" action="{{ route('templates.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-medium">Judul Template</label>
                            <input type="text" id="title" name="title" class="w-full mt-1 p-2 border rounded-md" placeholder="Masukkan judul template" required>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-gray-700 font-medium">Konten Surat</label>
                            <textarea id="content" name="content" class="w-full mt-1 p-2 border rounded-md" rows="10" placeholder="Masukkan konten surat" required></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                Simpan Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#konten',
            plugins: 'link image table code',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image table | code'
        });
    </script>
</x-app-layout>
