<?php
// app/Http/Controllers/CounselingAppointmentController.php

namespace App\Http\Controllers;

use App\Models\CounselingAppointments;
use App\Models\CounselingTimeSlot;
use App\Models\SupportNeeded;
use App\Models\ModeOfCounseling;
use Illuminate\Http\Request;

class CounselingAppointmentController extends Controller
{
    public function create()
{
    $timeSlots         = CounselingTimeSlot::active()->get();
    $supportNeeds      = SupportNeeded::active()->get();
    $modeOfCounselings = ModeOfCounseling::active()->get();
    $student           = auth()->user()->student ?? null;

    return view('appointment', compact('timeSlots', 'supportNeeds', 'modeOfCounselings', 'student'));
}   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name'             => 'required|string|max:500',
            'first_name'            => 'nullable|string|max:500',
            'middle_name'           => 'nullable|string|max:500',
            'course_and_year'       => 'nullable|string|max:500',
            'contact_no'            => 'nullable|string|max:20',
            'present_address'       => 'nullable|string|max:200',
            'counseling_date'       => 'nullable|date',
            'time_slot_id'          => 'required|exists:counseling_time_slots,id',
            'support_needed_id'     => 'nullable|exists:support_neededs,id',
            'mode_of_counseling_id' => 'nullable|exists:mode_of_counselings,id',
            'concern'               => 'nullable|string',
        ]);

        // ── Auto-link to the logged-in student ───────────────────────────────
        $validated['student_id'] = auth()->user()->student?->id;
        // ─────────────────────────────────────────────────────────────────────

        $appointment = CounselingAppointments::create($validated);

        // ── Notify all Admin & Guidance users in Filament dashboard ──────────
        $appointment->notifyAdmin('new_appointment');
        // ─────────────────────────────────────────────────────────────────────

        return redirect()
            ->route('guidance.appointment')
            ->with('success', 'Appointment submitted successfully!');
    }

    public function slots(Request $request): \Illuminate\Http\JsonResponse
{
    $date  = $request->query('date');
    $slots = \App\Models\CounselingTimeSlot::where('is_active', true)
        ->orderBy('name')
        ->get();

    $reserved = \App\Models\CounselingAppointments::query()
        ->whereDate('counseling_date', $date)
        ->pluck('time_slot_id')
        ->toArray();

    return response()->json(
        $slots->map(fn ($s) => [
            'id'       => $s->id,
            'name'     => $s->name,
            'reserved' => in_array($s->id, $reserved),
        ])
    );
}

    public function index()
    {
        $appointments = CounselingAppointments::latest()->paginate(15);
        return view('appointment', compact('appointments'));
    }

    public function cancel(CounselingAppointments $appointment): \Illuminate\Http\RedirectResponse
    {
        abort_if(
            $appointment->student_id !== auth()->user()->student?->id,
            403
        );

        abort_if(
            !in_array($appointment->status, ['pending', 'approved']),
            422
        );

        $appointment->update(['status' => 'cancelled']);

        // ── Notify all Admin & Guidance users in the Filament panel bell ─────
        $appointment->notifyAdmin('cancelled');
        // ─────────────────────────────────────────────────────────────────────

        return back()->with(
            'appointment_cancelled',
            'Your appointment on ' . $appointment->counseling_date->format('F d, Y') . ' has been cancelled.'
        );
    }
}