<?php

namespace App\Livewire\Frontend;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class PatientLogin extends Component
{
    public string $identifier = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => __('Please enter your NIK, Medical Record Number, or Email.'),
            'password.required' => __('Please enter your account password.'),
        ]);

        // Rate limiting: 5 attempts per minute
        $throttleKey = 'patient-login:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('identifier', __('Too many login attempts. Please wait :seconds seconds.', ['seconds' => $seconds]));

            return;
        }

        $patient = Patient::where('email', $this->identifier)
            ->orWhere('national_id', $this->identifier)
            ->orWhere('medical_record_number', $this->identifier)
            ->first();

        if (! $patient || ! Hash::check($this->password, $patient->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('identifier', __('Credentials do not match our medical records.'));

            return;
        }

        RateLimiter::clear($throttleKey);
        Auth::guard('patient')->login($patient, $this->remember);
        session()->regenerate();

        $this->redirect(
            session()->pull('url.intended', route('patient.dashboard')),
            navigate: false,
        );
    }

    public function render()
    {
        return view('livewire.frontend.patient-login');
    }
}
