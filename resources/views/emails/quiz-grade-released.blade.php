<x-mail::message>
# Hello {{ $studentName }},

Your quiz result for **{{ $quizTitle }}** in **{{ $courseTitle }}** has been released.

**Score:** {{ $score }}/{{ $maxScore }}

<x-mail::button :url="$courseId ? route('courses.show', $courseId) : '#'">
View Course
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
