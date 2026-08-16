<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Exam;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\RequirementSubmissionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CounselingAppointmentController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\CounselingLogformsPrintController;
use App\Http\Controllers\ReportsPrintController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamResultSlipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AccomplishmentReportController;

// ─────────────────────────────────────────────────────────────
// Public Routes
// ─────────────────────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome', [
        'exam' => Exam::first(),
        'scholarships' => \App\Models\TypeOfScholarship::active()->orderBy('name')->get(),
    ]);
});

Route::get('/gvc', function () {
    return view('welcome', [
        'exam' => Exam::first(),
        'scholarships' => \App\Models\TypeOfScholarship::active()->orderBy('name')->get(),
    ]);
})->name('gvc');

Route::get('/guest', [GuestController::class, 'showForm'])->name('guest.form');
Route::post('/guest', [GuestController::class, 'submitForm'])->name('guest.form.post');

// ─────────────────────────────────────────────────────────────
// Auth Routes
// ─────────────────────────────────────────────────────────────

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $role = strtolower(Auth::user()->role?->name ?? '');

        // Only students and guests may log in through this portal.
        // Admin, guidance, and scholarship accounts must use their own panels.
        if (!in_array($role, ['student', 'guest'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This login is for students only. Please use the appropriate portal for your account.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($role === 'guest') {
            return redirect()->route('referral');
        }

        return redirect()->route('gvc');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

// Public referral form (for guests arriving via direct link before login)
Route::get('/referral', [ReferralController::class, 'create'])->name('referral');

// ─────────────────────────────────────────────────────────────
// Protected Routes — all authenticated users
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Guidance portal
    Route::get('/guidance', function () {
        return view('guidance', [
            'student' => auth()->user()->student ?? null,
        ]);
    })->name('guidance');

    Route::get('/guidance/appointment', function (Request $request) {
        if (auth()->user()->role?->name === 'guest') {
            return redirect()->route('guidance')->with('error', 'Guests are not allowed to book appointments.');
        }
        return app(CounselingAppointmentController::class)->create($request);
    })->name('guidance.appointment');

    Route::post('/appointments', [CounselingAppointmentController::class, 'store'])->name('appointments.post');
    Route::get('/appointments', [CounselingAppointmentController::class, 'index'])->name('appointments.index');

    Route::patch('/appointments/{appointment}/cancel', [CounselingAppointmentController::class, 'cancel'])
        ->name('guidance.appointment.cancel');

    Route::get('/guidance/appointment/slots', [CounselingAppointmentController::class, 'slots'])
        ->name('appointments.slots');

    // ── Referrals — accessible to ALL authenticated users (enrolled, unenrolled, guest) ──
    Route::get('/guidance/referrals/create', [ReferralController::class, 'create'])->name('guidance_referrals.get');
    Route::post('/guidance/referrals', [ReferralController::class, 'store'])->name('guidance_referrals.post');
    Route::get('/guidance/referrals', [ReferralController::class, 'index'])->name('guidance_referrals.index');

    // Scholarship portal
    Route::get('/scholarship', [ApplicationController::class, 'index'])->name('scholarship');

    // ── Accomplishment Reports — scholars under Talents, SSG, or Sports only ──
    // Eligibility (scholarship type) is checked inside the controller itself,
    // not via middleware, since it depends on the scholar's DB record rather
    // than a fixed role.
    Route::get('/accomplishment-reports', [AccomplishmentReportController::class, 'index'])
        ->name('accomplishment_reports.get');
    Route::post('/accomplishment-reports', [AccomplishmentReportController::class, 'store'])
        ->name('accomplishment_reports.store');

    // Exam Routes
    Route::get('/exam/{exam}/admission',  [ExamController::class, 'admissionForm'])->name('exam.admission');
    Route::post('/exam/{exam}/admission', [ExamController::class, 'admissionStore'])->name('exam.admission.store');
    Route::get('/exam/{exam}/scope',      [ExamController::class, 'scope'])->name('exam.scope');
    Route::get('/exam/{exam}',            [ExamController::class, 'show'])->name('exam.show');
    Route::post('/exam/{exam}/submit',    [ExamController::class, 'submit'])->name('exam.submit');
    Route::post('/exam/{exam}/violation', [ExamController::class, 'recordViolation'])->name('exam.violation');
    Route::get('/exam/result/{result}',   [ExamController::class, 'result'])->name('exam.result');

    // Print
    Route::get('/counseling-logforms/print', [CounselingLogformsPrintController::class, 'print'])
        ->name('counseling-logforms.print');

    Route::get('/reports/print', ReportsPrintController::class)
        ->name('reports.print');

    Route::get('/scholars/print/institutional', [App\Http\Controllers\ScholarsController::class, 'printInstitutional'])
        ->name('scholars.print.institutional');

    Route::get('/exam-attempts/{examAttempt}/print', [ExamResultSlipController::class, 'print'])
        ->name('exam-attempts.print');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.readAll');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])
        ->name('notifications.fetch');

    Route::patch('/referral-invitations/{invitation}/respond',
        function (\App\Models\ReferralInvitation $invitation, \Illuminate\Http\Request $request) {
            abort_if(!auth()->user()->student, 403);
            $request->validate(['status' => 'required|in:accepted,declined']);
            $invitation->update(['status' => $request->status]);
            return back()->with('success', 'Response recorded.');
        }
    )->name('referral.invitation.respond');
});

// ─────────────────────────────────────────────────────────────
// Scholarship Routes — enrolled students only
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'enrolled'])->group(function () {
    Route::get('/apply/new', [ApplicationController::class, 'create'])->name('application_new.get');
    Route::post('/apply/new', [ApplicationController::class, 'store'])->name('application_new.post');
    Route::get('/application/renewal', [ApplicationController::class, 'renewalForm'])->name('application_renewal');
    Route::post('/application/renewal', [ApplicationController::class, 'storeRenewal'])->name('application_renewal.post');
    Route::get('/apply/requirements', [RequirementSubmissionController::class, 'index'])->name('requirements_submission.get');
    Route::post('/apply/requirements', [RequirementSubmissionController::class, 'store'])->name('requirements_submission.post');
});

// ─────────────────────────────────────────────────────────────
// Print Routes (no auth required)
// ─────────────────────────────────────────────────────────────
Route::get('/endorsement/{id}/print', function ($id) {
    $record = \App\Models\CounselingAppointments::with(['endorsement.personnel'])->findOrFail($id);
    return view('print.endorsement', compact('record'));
})->name('endorsement.print');

Route::get('/referral/{id}/endorsement/print', function ($id) {
    $record = \App\Models\Referrals::with(['endorsement.personnel'])->findOrFail($id);
    return view('print.endorsement', compact('record'));
})->name('referral.endorsement.print');

// ─────────────────────────────────────────────────────────────
// Dev / Debug Routes
// ─────────────────────────────────────────────────────────────
Route::get('/test-auth', function () {
    return response()->json([
        'logged_in' => auth()->check(),
        'user'      => auth()->user()?->email,
        'guard'     => 'web',
    ]);
});

Route::get('/test-calendar-direct', function () {
    return app(\App\Filament\Resources\CalendarResource\Pages\CalendarAppointments::class)->__invoke(request());
});

Route::get('/admin-calendar', function () {
    return app(\App\Filament\Resources\CalendarResource\Pages\CalendarAppointments::class)->__invoke(request());
})->middleware(['web', 'auth']);

Route::get('/admin-calendar/date/{date}', function ($date) {
    request()->merge(['date' => $date]);
    return app(\App\Filament\Resources\CalendarResource\Pages\ManageDatePage::class)->__invoke(request());
})->middleware(['web', 'auth']);