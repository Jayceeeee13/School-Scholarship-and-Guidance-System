<?php

namespace App\Http\Middleware;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops a user from opening or restarting an exam they have already finished.
 * Only COMPLETED attempts count, so someone mid-exam can still refresh the page.
 */
class BlockExamRetake
{
    public function handle(Request $request, Closure $next): Response
    {
        $exam   = $request->route('exam');
        $examId = $exam instanceof Exam ? $exam->id : $exam;

        $alreadyTaken = $request->user() && ExamAttempt::where('user_id', $request->user()->id)
            ->where('exam_id', $examId)
            ->whereNotNull('completed_at')
            ->exists();

        if ($alreadyTaken) {
            return redirect()->route('scholarship')
                ->with('error', 'You have already taken this exam.');
        }

        return $next($request);
    }
}