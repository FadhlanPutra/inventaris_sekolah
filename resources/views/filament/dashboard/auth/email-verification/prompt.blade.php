<x-filament-panels::page.simple>
    {{-- Konten default prompt --}}
    {{ $this->form }}

    {{-- Pesan custom --}}
    <div class="text-sm text-center text-gray-500">
        <p>
            We've sent an email to <b class="text-green-500">{{ $user_email }}</b> containing a verification link. Check your spam folder if you don't see the email.
        </p>
    </div>

    {{-- Tombol khusus “Not received email? Resend” --}}
    <div class="flex flex-row items-center justify-center gap-2 text-sm">
        <p class="text-gray-500">Not re ceived email?</p>
        <button wire:click="resend" class="filament-button filament-button-primary">
            {{ __('Resend') }}
        </button>
    </div>

    {{-- Tombol logout --}}
    <div class="flex flex-row items-center justify-center gap-2 text-sm">
        <span class="text-gray-500">Wrong account?</span>
        <form method="POST" action="{{ route('filament.dashboard.auth.logout') }}">
            @csrf
            <button type="submit" class="filament-button filament-button-primary">
                Logout
            </button>
        </form>
    </div>
</x-filament-panels::page.simple>
