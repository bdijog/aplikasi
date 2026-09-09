<?php

use App\Livewire\Frontend\AnnouncementDetail;
use App\Livewire\Frontend\AnnouncementList;
use App\Livewire\Frontend\AppointmentBooking;
use App\Livewire\Frontend\DoctorSchedule;
use App\Livewire\Frontend\HomePage;
use App\Livewire\Frontend\PatientDashboard;
use App\Livewire\Frontend\PatientLogin;
use App\Livewire\Frontend\PatientQueue;
use App\Livewire\Frontend\QueueDisplay;
use App\Livewire\Frontend\SelfCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Klinik Ayo Sehat Frontend Portal
|--------------------------------------------------------------------------
*/

// Home / Landing Page
Route::get('/', HomePage::class)->name('home');

// Doctor Schedule & Polyclinics
Route::get('/doctors', DoctorSchedule::class)->name('doctors.index');

// Integrated Patient Registration & Appointment Booking
Route::get('/booking', AppointmentBooking::class)->name('booking.index');

// Patient Live Queue Tracker
Route::get('/patient/queue', PatientQueue::class)->name('queue.index');

// Waiting Room TV Monitor Display
Route::get('/queue/display', QueueDisplay::class)->name('queue.display');

// Kiosk Self Check-in
Route::get('/check-in', SelfCheckIn::class)->name('checkin.index');

// Announcements & News
Route::get('/announcements', AnnouncementList::class)->name('announcements.index');
Route::get('/announcements/{slug}', AnnouncementDetail::class)->name('announcements.show');

// Language Switcher
Route::get('/locale/{lang}', function (string $lang, Request $request) {
    if (in_array($lang, ['id', 'en'], true)) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }

    return redirect()->back();
})->name('locale.switch');

// Patient Auth (Login Mandiri - Guest Only)
Route::middleware('guest:patient')->group(function () {
    Route::get('/patient/login', PatientLogin::class)->name('patient.login');
});

// Patient Authenticated (Dashboard & Profile)
Route::middleware('auth:patient')->group(function () {
    Route::get('/patient/dashboard', PatientDashboard::class)->name('patient.dashboard');
});

// Patient Logout
Route::post('/patient/logout', function (Request $request) {
    Auth::guard('patient')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home')->with('success', __('You have been successfully signed out of your patient account.'));
})->name('patient.logout');
