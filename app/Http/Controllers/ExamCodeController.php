<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ExamCodeController extends Controller
{
    public function verify(Request $request, Exam $exam)
    {
        // No code set: nothing to check.
        if (blank($exam->access_code)) {
            return redirect()->route('exam.admission', $exam->id);
        }

        $request->validate(['exam_code' => 'required|string|max:20']);

        $key = 'exam-code:' . $request->user()->id . ':' . $exam->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'exam_code' => "Too many attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $input = strtoupper(trim($request->input('exam_code')));

        if (! hash_equals(strtoupper($exam->access_code), $input)) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['exam_code' => 'Incorrect exam code.']);
        }

        RateLimiter::clear($key);

        // Storing a hash means a regenerated code invalidates old sessions.
        session(['exam_code_ok.' . $exam->id => md5($exam->access_code)]);

        return redirect()->route('exam.admission', $exam->id);
    }
}