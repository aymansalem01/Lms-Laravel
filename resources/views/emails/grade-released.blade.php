<x-mail::message>
# Hello {{ $studentName }},

Your assignment **{{ $assignmentTitle }}** in **{{ $courseTitle }}** has been graded.

**Score:** {{ $score }}/100

@if($feedback)
**Feedback:**
{{ $feedback }}
@endif

<x-mail::button :url="$courseId ? route('courses.show', $courseId) : '#'">
View Course
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
