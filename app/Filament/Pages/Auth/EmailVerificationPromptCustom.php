<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BasePrompt;

class EmailVerificationPromptCustom extends BasePrompt
{
    // Override view statis ke blade custom kita
    protected static string $view = 'filament.dashboard.auth.email-verification.prompt';

    public function logout()
    {
        auth()->logout();

        return redirect()->route('filament.dashboard.auth.login');
    }

    protected function getViewData(): array
    {
        $user = $this->getVerifiable(); // ini method dari base class yang mengembalikan user MustVerifyEmail

        return array_merge(parent::getViewData(), [
            'custom_message' => 'If the email is incorrect, you can log out and register again.',
            'user_email' => $user->email,
        ]);
    }

        public function resend(): void
    {
        // panggil method bawaan untuk mengirim ulang verifikasi email
        $this->resendNotificationAction()->call();
    }
}
