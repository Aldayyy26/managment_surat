<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Template Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('template_surat.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Buat Template Baru</a>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-auto w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Judul</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($templates as $template)
                            <tr>
                                <td class="border px-4 py-2">{{ $template->id }}</td>
                                <td class="border px-4 py-2">{{ $template->judul }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('template_surat.edit', $template) }}" class="bg-green-500 text-white px-4 py-2 rounded">Edit</a>
                                    <form action="{{ route('template_surat.destroy', $template) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
