<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Template Surat') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('
                    success ') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
            @endif

            <div class="mb-4 flex justify-between items-center">
                <form action="{{ route('surats.index') }}" method="GET" class="w-1/3">
                    <input type="text" name="search" placeholder="Cari nama surat..." value="{{ request('search') }}" class="w-full border rounded px-4 py-2" />
                </form>
                <a href="{{ route('surats.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Upload Template Baru
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">No</th>
                            <th class="border border-gray-300 px-4 py-2">No Jenis Surat</th>
                            <th class="border border-gray-300 px-4 py-2">Nama Surat</th>
                            <th class="border border-gray-300 px-4 py-2">Jenis Pengguna</th>
                            <th class="border border-gray-300 px-4 py-2">Jumlah Placeholder</th>
                            <th class="border border-gray-300 px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surats as $index => $surat)
                        @php
                        $placeholders = [];
                        if (!empty($surat->placeholders)) {
                        if (is_string($surat->placeholders)) {
                        $decoded = json_decode($surat->placeholders, true);
                        $placeholders = is_array($decoded) ? $decoded : [];
                        } elseif (is_array($surat->placeholders) || $surat->placeholders instanceof \Countable) {
                        $placeholders = $surat->placeholders;
                        }
                        }
                        @endphp

                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $surats->firstItem() + $index }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $surat->no_jenis_surat }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $surat->nama_surat }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ ucfirst($surat->user_type) }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ count($placeholders) }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <a href="{{ route('surats.edit', $surat->id) }}" class="text-blue-600 hover:underline mr-2">Edit</a>

                                <form id="delete-form-{{ $surat->id }}" action="{{ route('surats.destroy', $surat->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:underline btn-delete" data-id="{{ $surat->id }}">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $surats->links() }}
                </div>
            </div>
        </div>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.btn-delete');

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const suratId = this.getAttribute('data-id');

                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: "Data ini tidak bisa dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById(`delete-form-${suratId}`).submit();
                            }
                        });
                    });
                });
            });
        </script>
</x-app-layout>