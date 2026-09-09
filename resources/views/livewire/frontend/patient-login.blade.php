<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Logo & Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-surface-card shadow-sm border border-outline-variant/30 mb-5">
                <img alt="Klinik Ayo Sehat Logo" class="h-10 w-auto object-contain" src="{{ asset('images/logo.png') }}"/>
            </div>
            <h1 class="font-heading text-2xl md:text-3xl font-bold text-on-surface">
                {{ __('Patient Login') }}
            </h1>
            <p class="text-sm text-on-surface-variant mt-2 leading-relaxed max-w-sm mx-auto">
                {{ __('Sign in to your patient account to view appointments, queue status, and manage your visit history.') }}
            </p>
        </div>

        {{-- Login Card --}}
        <div class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm border border-outline-variant/30">
            <form wire:submit="login" class="space-y-5">
                {{-- Identifier --}}
                <div>
                    <label for="identifier" class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                        {{ __('National ID (NIK), Medical Record Number (MRN), or Email') }}
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-outline">badge</span>
                        <input
                            wire:model="identifier"
                            type="text"
                            id="identifier"
                            placeholder="{{ __('Example: 320123... or RM-2026-...') }}"
                            autocomplete="username"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary/30 {{ $errors->has('identifier') ? 'border-error bg-error-container/10 text-error' : 'border-outline-variant/40 bg-surface-container-lowest text-on-surface focus:border-primary' }}"
                        />
                    </div>
                    @error('identifier')
                        <p class="text-xs text-error mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1.5">
                        {{ __('Patient Account Password') }}
                    </label>
                    <div class="relative" x-data="{ showPassword: false }">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-outline">lock</span>
                        <input
                            wire:model="password"
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            placeholder="{{ __('Enter password') }}"
                            autocomplete="current-password"
                            class="w-full pl-10 pr-10 py-3 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary/30 {{ $errors->has('password') ? 'border-error bg-error-container/10 text-error' : 'border-outline-variant/40 bg-surface-container-lowest text-on-surface focus:border-primary' }}"
                        />
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors">
                            <span class="material-symbols-outlined text-[20px]" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-error mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/30"/>
                        <span class="text-xs font-medium text-on-surface-variant">{{ __('Remember Me') }}</span>
                    </label>
                    <a href="https://wa.me/62812345678?text={{ urlencode(__('I forgot my patient account password. Please assist.')) }}" target="_blank" class="text-xs font-medium text-primary hover:text-primary-container transition-colors">
                        {{ __('Forgot Password?') }}
                    </a>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 rounded-xl bg-primary text-on-primary font-bold text-sm shadow-md hover:bg-primary-container hover:shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="login">
                        <span class="material-symbols-outlined text-[20px]">login</span>
                    </span>
                    <span wire:loading wire:target="login">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="login">{{ __('Sign In') }}</span>
                    <span wire:loading wire:target="login">{{ __('Processing') }}...</span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-outline-variant/30"></div>
                <span class="text-[11px] text-on-surface-variant font-medium uppercase tracking-wider">{{ __('or') }}</span>
                <div class="flex-1 h-px bg-outline-variant/30"></div>
            </div>

            {{-- Register CTA --}}
            <a href="{{ route('booking.index') }}" class="w-full py-3 rounded-xl border-2 border-primary/20 text-primary font-bold text-sm hover:bg-primary-container/10 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                <span>{{ __('Register & Book Appointment') }}</span>
            </a>
        </div>

        {{-- Security Badge --}}
        <div class="mt-6 flex items-center justify-center gap-2 text-[11px] text-on-surface-variant">
            <span class="material-symbols-outlined text-[16px] text-status-available">shield</span>
            <span>{{ __('Encrypted System & MoH Standard Medical Records') }}</span>
        </div>

        {{-- WhatsApp Help --}}
        <div class="mt-3 text-center">
            <a href="https://wa.me/628123456789" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-primary font-medium hover:text-primary-container transition-colors">
                <span class="material-symbols-outlined text-[16px]">chat</span>
                <span>{{ __('Need Help? Contact WhatsApp Admission') }}</span>
            </a>
        </div>
    </div>
</div>
