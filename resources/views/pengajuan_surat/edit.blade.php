<!DOCTYPE html>
<html lang="id" x-data="{ open: false, userDropdown: false }" @click.away="userDropdown = false">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Pengajuan Surat</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex">
                    <a href="{{ url('/') }}" class="text-lg font-bold text-gray-900">SPETI</a>
                </div>

                <div class="flex items-center">
                    @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-gray-700 font-medium focus:outline-none">
                            {{ Auth::user()->name }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50"
                            style="display: none;"
                        >
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                    Log Out
                                </a>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ url('login') }}" class="btn-getstarted px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Title -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-semibold leading-tight text-gray-900">Edit Pengajuan Surat</h2>
        </div>
    </header>
    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 text-green-700 bg-green-100 border border-green-400 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pengajuan_surat.update', $pengajuan->id) }}" method="POST" class="p-6 bg-white rounded shadow" novalidate>
                @csrf
                @method('PUT')

                <!-- Template Surat (readonly) -->
                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Template Surat</label>
                    <input type="text" value="{{ $pengajuan->template->nama_surat }}" readonly
                           class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
                    <input type="hidden" name="template_id" value="{{ $pengajuan->template_id }}">
                </div>

                <!-- Dynamic Placeholder Fields -->
                <div id="placeholderFields" class="mt-4"></div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                        Update Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <div id="data-container"
        data-existing-content='@json($konten)'
        data-template-id="{{ $pengajuan->template_id }}"
        style="display:none;">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('data-container');
        const existingContent = JSON.parse(container.getAttribute('data-existing-content') || '{}');
        const templateId = parseInt(container.getAttribute('data-template-id')) || null;

        const placeholderFields = document.getElementById('placeholderFields');
        placeholderFields.innerHTML = '';

        if (templateId !== null) {
            fetch(`/pengajuan_surat/get-placeholders/${templateId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not OK');
                    }
                    return response.json();
                })
                .then(placeholders => {
                    Object.entries(placeholders).forEach(([key, config]) => {
                        let fieldHTML = '';
                        const labelHTML = `<label class="block mb-1 font-semibold text-gray-700" for="konten_${key}">${config.label}</label>`;
                        const value = existingContent[key] ?? '';

                        if (['text', 'number', 'date'].includes(config.type)) {
                            fieldHTML = `<input
                                id="konten_${key}"
                                type="${config.type}"
                                name="konten[${key}]"
                                value="${value}"
                                class="w-full border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                                required
                            />`;
                        } else if (config.type === 'textarea') {
                            fieldHTML = `<textarea
                                id="konten_${key}"
                                name="konten[${key}]"
                                rows="3"
                                class="w-full border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                                required>${value}</textarea>`;
                        } else if (config.type === 'select') {
                            const optionsHTML = config.options.map(opt => {
                                const selected = (opt == value) ? 'selected' : '';
                                return `<option value="${opt}" ${selected}>${opt}</option>`;
                            }).join('');
                            fieldHTML = `<select
                                id="konten_${key}"
                                name="konten[${key}]"
                                class="w-full border rounded px-2 py-1 focus:outline-none focus:ring focus:ring-blue-300"
                                required>${optionsHTML}</select>`;
                        }

                        placeholderFields.innerHTML += `
                            <div class="mb-4">
                                ${labelHTML}
                                ${fieldHTML}
                            </div>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Gagal mengambil data placeholder:', error);
                    placeholderFields.innerHTML = `<p class="text-red-500 font-semibold">Gagal memuat input form.</p>`;
                });
        }
    });

    </script>

</body>
</html>
