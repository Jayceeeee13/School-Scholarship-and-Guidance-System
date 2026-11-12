<?php

namespace App\Http\Middleware;

use App\Models\Exam;
use Closure;
use Illuminate\Http\Request;

class EnsureExamCodeVerified
{
    public function handle(Request $request, Closure $next)
    {
        $param = $request->route('exam');
        $exam  = $param instanceof Exam ? $param : Exam::find($param);

        if (! $exam || blank($exam->access_code)) {
            return $next($request);
        }

        if (session('exam_code_ok.' . $exam->id) === md5($exam->access_code)) {
            return $next($request);
        }

        return redirect(url('/gvc') . '#home')
            ->withErrors(['exam_code' => 'Please enter the exam code first.']);
    }
}