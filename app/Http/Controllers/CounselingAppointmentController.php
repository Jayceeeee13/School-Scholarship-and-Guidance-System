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

        // ── Server-side double-booking check ─────────────────────────────────
        // Cancelled / rejected appointments free up their slot.
        if (! empty($validated['counseling_date'])) {
            $taken = CounselingAppointments::whereDate('counseling_date', $validated['counseling_date'])
                ->where('time_slot_id', $validated['time_slot_id'])
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->exists();

            if ($taken) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'time_slot_id' => 'That time slot was just taken. Please choose another.',
                    ]);
            }
        }
        // ─────────────────────────────────────────────────────────────────────

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
        $date = $request->query('date');

        $reserved = CounselingAppointments::query()
            ->whereDate('counseling_date', $date)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->pluck('time_slot_id')
            ->toArray();

        // Sort chronologically (9am → 11am → 1pm → 2pm), not alphabetically.
        $slots = CounselingTimeSlot::where('is_active', true)
            ->get()
            ->sortBy(fn ($s) => $this->slotStartMinutes($s->name))
            ->values();

        return response()->json(
            $slots->map(fn ($s) => [
                'id'       => $s->id,
                'name'     => $s->name,
                'reserved' => in_array($s->id, $reserved),
            ])
        );
    }

    /**
     * Converts "9:00am-10:00am" / "11:00am-12:00nn" into minutes since
     * midnight so slots can be sorted chronologically.
     */
    private function slotStartMinutes(string $name): int
    {
        if (! preg_match('/^\s*(\d{1,2}):(\d{2})\s*(am|pm|nn)/i', $name, $m)) {
            return 0;
        }

        $hour   = (int) $m[1];
        $period = strtolower($m[3]) === 'nn' ? 'pm' : strtolower($m[3]);

        if ($period === 'pm' && $hour !== 12) {
            $hour += 12;
        }
        if ($period === 'am' && $hour === 12) {
            $hour = 0;
        }

        return $hour * 60 + (int) $m[2];
    }

    public function index()
    {
        $appointments = CounselingAppointments::latest()->paginate(15);
        return view('appointment', compact('appointments'));
    }

    public function cancel(Request $request, CounselingAppointments $appointment): \Illuminate\Http\RedirectResponse
    {
        abort_if(
            $appointment->student_id !== auth()->user()->student?->id,
            403
        );

        abort_if(
            ! in_array($appointment->status, ['pending', 'approved']),
            422
        );

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $appointment->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        // ── Notify all Admin & Guidance users in the Filament panel bell ─────
        $appointment->notifyAdmin('cancelled');
        // ─────────────────────────────────────────────────────────────────────

        return back()->with(
            'appointment_cancelled',
            'Your appointment on ' . $appointment->counseling_date->format('F d, Y') . ' has been cancelled.'
        );
    }
}