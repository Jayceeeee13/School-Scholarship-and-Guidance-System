<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Program;
use App\Models\ExamCategory;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::withCount('questions')->get();
        return view('welcome', compact('exams'));
    }

    public function admission(Exam $exam)
    {
        if (ExamAttempt::hasAttempted(Auth::id(), $exam->id)) {
            return redirect()
                ->route('exam.result', ExamAttempt::latestAttempt(Auth::id(), $exam->id))
                ->with('info', 'You have already completed this exam.');
        }

        return $this->admissionForm($exam);
    }

    public function admissionForm(Exam $exam)
    {
        $programs = Program::active()->orderBy('name')->get();
        $user     = Auth::user();

        $computedAge = $user->birthdate
            ? Carbon::parse($user->birthdate)->age
            : null;

        $formattedBirthdate = $user->birthdate
            ? Carbon::parse($user->birthdate)->format('Y-m-d')
            : null;

        return view('exam.admission-form', compact(
            'exam',
            'programs',
            'user',
            'computedAge',
            'formattedBirthdate'
        ));
    }

    public function admissionStore(Request $request, Exam $exam)
    {
        if (ExamAttempt::hasAttempted(Auth::id(), $exam->id)) {
            return redirect()
                ->route('exam.result', ExamAttempt::latestAttempt(Auth::id(), $exam->id))
                ->with('info', 'You have already completed this exam.');
        }

        $request->validate([
            'track_strand'     => 'required|string|max:150',
            'last_school'      => 'required|string|max:200',
            'age'              => 'required|integer|min:15|max:60',
            'birth_date'       => 'required|date',
            'address'          => 'required|string|max:255',
            'contact_number'   => 'required|string|max:20',
            'preferred_course' => 'required|string',
        ]);

        $user      = Auth::user();
        $nameParts = explode(' ', $user->name, 2);

        session(['admission_info' => array_merge($request->only([
            'track_strand', 'last_school', 'age', 'birth_date',
            'address', 'contact_number', 'preferred_course',
        ]), [
            'first_name' => $nameParts[0] ?? $user->name,
            'last_name'  => $nameParts[1] ?? '',
            'email'      => $user->email,
        ])]);

        return redirect()->route('exam.scope', $exam->id);
    }

    public function scope(Exam $exam)
    {
        if (ExamAttempt::hasAttempted(Auth::id(), $exam->id)) {
            return redirect()
                ->route('exam.result', ExamAttempt::latestAttempt(Auth::id(), $exam->id))
                ->with('info', 'You have already completed this exam.');
        }

        $categories = ExamCategory::active()
            ->withCount(['questions' => fn($q) => $q->where('exam_id', $exam->id)])
            ->having('questions_count', '>', 0)
            ->get();

        $totalItems = $categories->sum('questions_count');

        return view('exam.scope', compact('exam', 'categories', 'totalItems'));
    }

    public function show(Exam $exam)
    {
        if (ExamAttempt::hasAttempted(Auth::id(), $exam->id)) {
            return redirect()
                ->route('exam.result', ExamAttempt::latestAttempt(Auth::id(), $exam->id))
                ->with('info', 'You have already completed this exam.');
        }

        session()->forget('exam_violations_' . $exam->id . '_' . Auth::id());

        $categories = ExamCategory::active()
            ->whereHas('questions', fn($q) => $q->where('exam_id', $exam->id))
            ->with(['questions' => function ($q) use ($exam) {
                $q->where('exam_id', $exam->id)
                  ->with('choices');
            }])
            ->get()
            ->map(fn($cat) => [
                'name'      => $cat->name,
                'questions' => $cat->questions->values()->toArray(),
            ])
            ->values()
            ->toArray();

        return view('exam.show', compact('exam', 'categories'));
    }

    public function recordViolation(Request $request, Exam $exam)
    {
        $violation = $request->input('violation');

        if (!$violation) {
            return response()->json(['ok' => false, 'message' => 'No violation data']);
        }

        $sessionKey = 'exam_violations_' . $exam->id . '_' . Auth::id();
        $violations = session($sessionKey, []);
        $violations[] = $violation;
        session([$sessionKey => $violations]);

        return response()->json([
            'ok'    => true,
            'count' => count($violations),
        ]);
    }

    public function submit(Request $request, Exam $exam)
    {
        $submittedAnswers = $request->input('answers', []);

        $exam->load('questions.choices');

        $score         = 0;
        $totalPoints   = 0;
        $answerRecords = [];

        foreach ($exam->questions as $question) {
            $selectedChoiceId = $submittedAnswers[$question->id] ?? null;
            $totalPoints += $question->points ?? 1;

            $isCorrect = false;
            if ($selectedChoiceId) {
                $isCorrect = $question->choices
                    ->where('id', $selectedChoiceId)
                    ->where('is_correct', true)
                    ->isNotEmpty();
            }

            if ($isCorrect) {
                $score += $question->points ?? 1;
            }

            $answerRecords[] = [
                'exam_question_id'   => $question->id,
                'question_choice_id' => $selectedChoiceId,
                'is_correct'         => $isCorrect,
            ];
        }

        $percentage = $totalPoints > 0
            ? round(($score / $totalPoints) * 100, 2)
            : 0;

        // ── Calculate discount based on exam percentage ────────────────────
        // Thresholds match the Score Guidelines printed on the result slip:
        //   95–100 → 100% Tuition Fee & Misc. Discount  (stored as 100)
        //   85–94  → 100% Tuition Fee Discount           (stored as 85)
        //   75–84  → 75% Tuition Fee Discount            (stored as 75)
        //   65–74  → 50% Tuition Fee Discount            (stored as 50)
        //   60–64  → 25% Tuition Fee Discount            (stored as 25)
        //   50–59  → 10% Tuition Fee Discount            (stored as 10)
        //   <50    → No Discount                         (stored as 0)
        //
        // We store a plain integer "discount code" and use
        // ExamAttempt::resolveDiscount() everywhere to get the label,
        // so the label is always consistent across the result slip,
        // the admin panel, and the applicant benefit field.
        $discount = ExamAttempt::calculateDiscount($percentage);

        $userId         = Auth::id();
        $sessionKey     = 'exam_violations_' . $exam->id . '_' . $userId;
        $violations     = session($sessionKey, []);
        $violationCount = count($violations);

        $attempt = DB::transaction(function () use (
            $exam, $score, $totalPoints, $percentage, $discount,
            $answerRecords, $userId, $violations, $violationCount
        ) {
            $attempt = ExamAttempt::create([
                'exam_id'         => $exam->id,
                'user_id'         => $userId,
                'score'           => $score,
                'total_points'    => $totalPoints,
                'percentage'      => $percentage,
                'discount'        => $discount,
                'status'          => 'completed',
                'started_at'      => now(),
                'completed_at'    => now(),
                'violations'      => $violations,
                'violation_count' => $violationCount,
            ]);

            foreach ($answerRecords as $record) {
                ExamAnswer::create([
                    'exam_attempt_id'    => $attempt->id,
                    'exam_question_id'   => $record['exam_question_id'],
                    'question_choice_id' => $record['question_choice_id'],
                    'is_correct'         => $record['is_correct'],
                ]);
            }

            return $attempt;
        });

        session()->forget($sessionKey);

        return redirect()->route('exam.result', $attempt->id)
            ->with('success', 'Exam submitted successfully!');
    }

    public function result(ExamAttempt $result)
    {
        $result->load([
            'exam',
            'user',
            'answers.question.examCategory',
            'answers.question.choices',
            'answers.choice',
        ]);

        $percentage = (float) $result->percentage;

        $recommendedProgram = match (true) {
            $percentage >= 90 => Program::active()->where('name', 'like', '%Computer Science%')->first(),
            $percentage >= 80 => Program::active()->where('name', 'like', '%Information Technology%')->first(),
            $percentage >= 70 => Program::active()->where('name', 'like', '%Information Systems%')->first(),
            $percentage >= 60 => Program::active()->where('name', 'like', '%Computer Engineering%')->first(),
            $percentage >= 50 => Program::active()->where('name', 'like', '%Electronics%')->first(),
            default           => Program::active()->where('name', 'like', '%Associate%')->first(),
        };

        return view('exam.result', compact('result', 'recommendedProgram'));
    }
}