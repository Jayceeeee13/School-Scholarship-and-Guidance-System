<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Complete – {{ $result->exam->title ?? 'Exam' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">

<div class="max-w-lg mx-auto px-4 text-center">

    <div class="bg-white rounded-2xl shadow p-10">

        <div class="flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Exam Submitted!</h1>

        <p class="text-gray-500 mb-1">
            Thank you, <strong class="text-gray-700">{{ $result->user->name }}</strong>.
        </p>
        <p class="text-gray-500 mb-6">
            You have successfully completed the
            <strong class="text-gray-700">{{ $result->exam->title ?? 'exam' }}</strong>.
        </p>

        <p class="text-sm text-gray-400">
            Submitted on {{ \Carbon\Carbon::parse($result->completed_at)->format('F d, Y \a\t h:i A') }}
        </p>

        <div class="mt-8 p-4 bg-indigo-50 rounded-xl text-sm text-indigo-700">
            Your result slip will be released by the scholarship office. Please wait for further instructions.
        </div>

        <div class="mt-6">
            <a href="/gvc"
               class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">
                ← Back to Home
            </a>
        </div>

    </div>

</div>
</body>
</html>