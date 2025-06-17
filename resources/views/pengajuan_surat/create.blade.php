<!DOCTYPE html>
<html lang="id" x-data="{ open: false, userDropdown: false }" @click.away="userDropdown = false" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SPETI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/favicon/android-chrome-512x512.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">

</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="text-lg">HOME</a>
                    </div>
                </div>
                <!-- User Dropdown -->
                <div class="flex items-center">
                    @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-gray-700 font-medium">
                            {{ Auth::user()->name }} <i class="bi bi-chevron-down"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 bg-white border rounded shadow-lg w-40" style="display: none;">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700">Dashboard</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700">Log Out</a>
                            </form>
                        </div>
                    </div>
                    @else
                    <a class="btn-getstarted" href="{{ url('login') }}">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Title -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-semibold leading-tight">Ajukan Surat Sesuai Kebutuhanmu</h2>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('pengajuan_surat.store') }}" method="POST" class="p-6 bg-white rounded shadow">
                @csrf

                <div class="mb-4">
                    <label for="templateSurat" class="block mb-2 font-medium text-gray-700">Pilih Surat</label>
                    <select name="template_id" id="templateSurat" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih surat yang diajukan --</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->nama_surat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tempat input placeholder muncul -->
                <div id="placeholderFields" class="mt-4"></div>

                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('templateSurat').addEventListener('change', function() {
            const templateId = this.value;
            const placeholderFields = document.getElementById('placeholderFields');
            placeholderFields.innerHTML = '';

            if (!templateId) return;

            fetch(`/pengajuan_surat/get-placeholders/${templateId}`)
                .then(response => response.json())
                .then(placeholders => {

                    Object.entries(placeholders).forEach(([key, config]) => {
                        let fieldHtml = '';
                        const labelHtml = `<label for="konten_${key}" class="block mb-1 font-semibold">${config.label}</label>`;
                        const requiredAttr = config.nullable ? '' : 'required';

                        switch (config.type) {
                            case 'text':
                            case 'number':
                            case 'date':
                                fieldHtml = `<input type="${config.type}" id="konten_${key}" name="konten[${key}]" class="w-full border rounded px-2 py-1" ${requiredAttr}>`;
                                break;
                            case 'textarea':
                                fieldHtml = `<textarea id="konten_${key}" name="konten[${key}]" rows="3" class="w-full border rounded px-2 py-1" ${requiredAttr}></textarea>`;
                                break;
                            case 'select':
                                if (Array.isArray(config.options)) {
                                    const optionsHtml = config.options.map(opt =>
                                        `<option value="${opt}">${opt}</option>`
                                    ).join('');
                                    fieldHtml = `<select id="konten_${key}" name="konten[${key}]" class="w-full border rounded px-2 py-1" ${requiredAttr}>${optionsHtml}</select>`;
                                } else {
                                    fieldHtml = `<input type="text" id="konten_${key}" name="konten[${key}]" class="w-full border rounded px-2 py-1" ${requiredAttr}>`;
                                }
                                break;
                            default:
                                fieldHtml = `<input type="text" id="konten_${key}" name="konten[${key}]" class="w-full border rounded px-2 py-1" ${requiredAttr}>`;
                        }

                        placeholderFields.innerHTML += `
                        <div class="mb-4">
                            ${labelHtml}
                            ${fieldHtml}
                        </div>
                    `;
                    });
                })
                .catch(error => {
                    console.error('Gagal mengambil data placeholder:', error);
                    placeholderFields.innerHTML = '<p class="text-red-500">Gagal memuat input form.</p>';
                });
        });
    </script>

</body>

</html>