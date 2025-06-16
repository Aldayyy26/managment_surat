<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                {{-- Sapaan --}}
                <div class="mb-6 text-lg font-semibold">
                    Halo, {{ $salam }} {{ $user->name }}! 👋<br>
                    Selamat datang di website SPETI.
                </div>

                {{-- Super Admin & Admin Prodi --}}
                @role('super_admin|adminprodi')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-100 p-4 rounded shadow">Total User: {{ $totalUsers }}</div>
                    <div class="bg-green-100 p-4 rounded shadow">Total Surat: {{ $totalSurat }}</div>
                    <div class="bg-yellow-100 p-4 rounded shadow">Total Pengajuan: {{ $totalApply }}</div>
                </div>
                @endrole

                {{-- Kaprodi --}}
                @role('kepalaprodi')
                <div class="bg-yellow-100 p-4 rounded shadow mb-4">
                    Total Pengajuan Surat: {{ $totalApply }}
                </div>
                @endrole

                {{-- Mahasiswa & Dosen --}}
                @role('mahasiswa|dosen')
                <div class="bg-yellow-100 p-4 rounded shadow mb-4">
                    Jumlah Pengajuan Surat Anda: {{ $userApply }}
                </div>
                @endrole

            </div>
        </div>
    </div>
</x-app-layout>