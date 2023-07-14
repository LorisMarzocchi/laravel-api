<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" :value="{{ old('current_password') }}">Current Password</label>
            <input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            {{-- <error :messages="$errors->updatePassword->get('current_password')" class="mt-2" /> --}}
            @error('current_password')
                {{ $message }}
            @enderror

        </div>

        <div>
            <label for="password" :value="{{ old('password') }}">New Password</label>
            <input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            {{-- <error :messages="$errors->updatePassword->get('password')" class="mt-2" /> --}}
            @error('password')
                {{ $message }}
            @enderror
        </div>

        <div>
            <label for="password_confirmation" :value={{ old('password_confirmation') }}>Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            {{-- <error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" /> --}}
            @error('password_confirmation')
                {{ $message }}
            @enderror

        </div>

        <div class="flex items-center gap-4">
            <button>Save</button>

            @if (session('status') === 'password-updated')
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
