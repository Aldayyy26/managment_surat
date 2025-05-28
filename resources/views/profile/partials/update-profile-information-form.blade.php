<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Name (Non-editable) -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->name" disabled />
        </div>

        <!-- Email (Non-editable) -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100" :value="$user->email" disabled />
        </div>

        <!-- WhatsApp (Editable) -->
        <div>
            <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
            <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" :value="old('whatsapp_number', $user->whatsapp_number)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
        </div>

        <!-- Tambahkan pengecekan role -->

        @hasrole('mahasiswa')
            <!-- NIM -->
            <div>
                <x-input-label for="nim" :value="__('NIM')" />
                <x-text-input id="nim" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->nim" disabled />
            </div>

            <!-- Semester -->
            <div>
                <x-input-label for="semester" :value="__('Semester')" />
                <x-text-input id="semester" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->semester" disabled />
            </div>
        @endhasrole

        @hasanyrole('adminprodi|kepalaprodi|dosen')
            <!-- NIDN -->
            <div>
                <x-input-label for="nidn" :value="__('NIDN')" />
                <x-text-input id="nidn" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->nidn" disabled />
            </div>

            <!-- NIP -->
            <div>
                <x-input-label for="nip" :value="__('NIP')" />
                <x-text-input id="nip" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->nip" disabled />
            </div>
        @endhasanyrole

        <!-- Status -->
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <x-text-input id="status" type="text" class="mt-1 block w-full bg-gray-100" :value="$user->status" disabled />
        </div>

        <!-- Tombol -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
