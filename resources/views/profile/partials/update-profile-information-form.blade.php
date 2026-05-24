<section>
    <div class="space-y-6">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-5" enctype="multipart/form-data">
            @csrf
            @method('patch')

            {{-- Avatar Upload --}}
            <div class="form-group">
                <x-input-label :value="__('Foto Profil')" />
                <div class="mt-1.5 flex items-center gap-5">
                    <div class="relative">
                        @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-100">
                        @else
                        <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center border-2 border-gray-100">
                            <span class="text-white font-bold text-xl">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer transition-colors">
                        <p class="text-xs text-gray-400 mt-1.5">Format: JPG, PNG, WebP. Maks 2MB.</p>
                    </div>
                </div>
                <x-input-error class="mt-1.5" :messages="$errors->get('avatar')" />
            </div>

            {{-- Nama & Telepon --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="phone" :value="__('No. Telepon')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1.5 block w-full" :value="old('phone', $user->phone)" placeholder="08XXXXXXXXXX" autocomplete="tel" />
                    <x-input-error class="mt-1.5" :messages="$errors->get('phone')" />
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <p class="text-sm text-amber-700">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline font-medium text-amber-700 hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 rounded-md">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-emerald-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Tanggal Lahir & Jenis Kelamin --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <x-input-label for="birth_date" :value="__('Tanggal Lahir')" />
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="mt-1.5 block w-full rounded-lg border-gray-200 bg-gray-50/50 text-sm text-gray-700 placeholder-gray-400 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 focus:bg-white transition-colors">
                    <x-input-error class="mt-1.5" :messages="$errors->get('birth_date')" />
                </div>

                <div class="form-group">
                    <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                    <select id="gender" name="gender" class="mt-1.5 block w-full rounded-lg border-gray-200 bg-gray-50/50 text-sm text-gray-700 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 focus:bg-white transition-colors">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    <x-input-error class="mt-1.5" :messages="$errors->get('gender')" />
                </div>
            </div>

            {{-- Alamat --}}
            <div class="form-group">
                <x-input-label for="address" :value="__('Alamat')" />
                <textarea id="address" name="address" rows="2" class="mt-1.5 block w-full rounded-lg border-gray-200 bg-gray-50/50 text-sm text-gray-700 placeholder-gray-400 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 focus:bg-white transition-colors" placeholder="Alamat lengkap...">{{ old('address', $user->address) }}</textarea>
                <x-input-error class="mt-1.5" :messages="$errors->get('address')" />
            </div>

            {{-- Bio --}}
            <div class="form-group">
                <x-input-label for="bio" :value="__('Bio')" />
                <textarea id="bio" name="bio" rows="2" class="mt-1.5 block w-full rounded-lg border-gray-200 bg-gray-50/50 text-sm text-gray-700 placeholder-gray-400 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 focus:bg-white transition-colors" placeholder="Cerita singkat tentang Anda...">{{ old('bio', $user->bio) }}</textarea>
                <x-input-error class="mt-1.5" :messages="$errors->get('bio')" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-orange-500 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors">
                    {{ __('Simpan Perubahan') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-emerald-600 font-medium"
                    >{{ __('Tersimpan.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
