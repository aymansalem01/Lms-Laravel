<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use App\Services\Plagiarism\PlagiarismService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function store(Request $request, Course $course, Assignment $assignment)
    {
        $user = auth()->user();

        if ($user->isStudent()) {
            $isEnrolled = $course->students()->where('users.id', $user->id)->exists();
            if (!$isEnrolled) abort(403);
        } elseif (!$user->isInstructorOrAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'file_url'  => 'nullable|url|max:2048',
            'video_url' => 'nullable|url|max:2048',
            'audio_url' => 'nullable|url|max:2048',
            'link_url'  => 'nullable|url|max:2048',
            'link'      => 'nullable|url|max:2048',
            'file'      => 'nullable|file|max:10240',
            'notes'     => 'nullable|string',
        ]);

        if (!empty($data['link']) && empty($data['link_url'])) {
            $data['link_url'] = $data['link'];
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('submissions', 'public');
        }

        $data['assignment_id'] = $assignment->id;
        $data['student_id'] = $user->id;
        $data['status'] = 'submitted';
        $data['submitted_at'] = now();

        $submission = Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $user->id],
            $data
        );

        User::where('id', $course->instructor_id)
            ->each(function ($instructor) use ($course, $assignment, $user) {
            $instructor->notifications()->create([
                'type'    => 'submission',
                'title'   => 'Submission: ' . $assignment->title,
                'message' => $user->name . ' has submitted "' . $assignment->title . '" in ' . $course->title,
                'link'    => route('courses.assignments.show', [$course, $assignment]),
            ]);
        });

        return redirect()->route('courses.assignments.show', [$course, $assignment])
            ->with('success', 'Assignment submitted successfully.');
    }

    public function update(Request $request, Submission $submission)
    {
        $user = auth()->user();
        abort_if($submission->student_id !== $user->id, 403);

        $data = $request->validate([
            'file_url'  => 'nullable|url|max:2048',
            'video_url' => 'nullable|url|max:2048',
            'audio_url' => 'nullable|url|max:2048',
            'link_url'  => 'nullable|url|max:2048',
            'notes'     => 'nullable|string',
        ]);

        $data['submitted_at'] = now();
        $submission->update($data);

        return redirect()->route('courses.assignments.show', [$submission->assignment->course_id, $submission->assignment])
            ->with('success', 'Submission updated successfully.');
    }

    public function destroy(Submission $submission)
    {
        $user = auth()->user();
        abort_if($submission->student_id !== $user->id, 403);

        $submission->delete();

        return redirect()->route('courses.assignments.show', [$submission->assignment->course_id, $submission->assignment])
            ->with('success', 'Submission deleted successfully.');
    }

    public function checkPlagiarism(Submission $submission, PlagiarismService $plagiarismService)
    {
        $report = $plagiarismService->check($submission);

        if (request()->wantsJson()) {
            return response()->json([
                'report' => $report,
                'overall_similarity' => $report->overall_similarity,
                'ai_probability' => $report->ai_probability,
                'matches' => $report->matches,
                'status' => $report->status,
            ]);
        }

        return redirect()->back()->with('success', 'Plagiarism check completed.');
    }
}
