<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $courses = Course::with('instructor')->latest()->get();
        } elseif ($user->isInstructor()) {
            $courses = $user->taughtCourses()->with('instructor')->latest()->get();
        } else {
            $courses = $user->enrolledCourses()->with('instructor')->latest()->get();
        }

        return view('courses.index', compact('courses'));
    }

    public function catalog(Request $request)
    {
        $user = auth()->user();
        $enrolledIds = $user->enrolledCourses()->pluck('course_id');

        $courses = Course::with('instructor')
            ->where('is_published', true)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('program', 'like', "%{$search}%")
                      ->orWhereHas('instructor', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->filled('program'), function ($q) use ($request) {
                $q->where('program', $request->program);
            })
            ->withCount('enrollments')
            ->latest()
            ->get();

        if ($request->has('live')) {
            return view('courses._catalog_results', compact('courses', 'enrolledIds'));
        }

        return view('courses.catalog', compact('courses', 'enrolledIds'));
    }

    public function show(Course $course)
    {
        $user = auth()->user();

        $course->loadMissing([
            'instructor',
            'modules.lessons',
            'modules.quizzes',
            'modules.liveSessions',
            'modules.assignments',
            'modules.moduleFiles',
            'assignments.submissions',
            'quizzes',
            'announcements.author',
            'attendance',
            'discussions.user',
            'groups.students',
            'gradeRules',
        ]);

        $discussions = $course->discussions()->with('user')->withCount('replies')->latest()->get();
        $attendance = $course->attendance()->with('student')->latest()->take(50)->get();
        $assignments = $course->assignments;
        $quizzes = $course->quizzes;
        $modules = $course->modules;
        $liveSessions = $course->liveSessions;
        $isEnrolled = $user->role === 'student' && $course->students()->where('users.id', $user->id)->exists();
        $course->load('students');

        $moduleProgress = collect();
        if ($user->isStudent() && $isEnrolled) {
            $lessonIds = $course->modules->flatMap->lessons->pluck('id');
            $completedLessonIds = LessonProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->where('completed', true)
                ->pluck('lesson_id');

            $moduleProgress = $course->modules->mapWithKeys(function ($module) use ($completedLessonIds) {
                $total = $module->lessons->count();
                $done = $module->lessons->filter(fn($l) => $completedLessonIds->contains($l->id))->count();
                return [$module->id => ['total' => $total, 'completed' => $done, 'percent' => $total > 0 ? round(($done / $total) * 100) : 0]];
            });
        }

        $enrolledIds = $course->students()->pluck('users.id');
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get();

        return view('courses.show', compact('course', 'discussions', 'attendance', 'assignments', 'quizzes', 'modules', 'liveSessions', 'isEnrolled', 'moduleProgress', 'availableStudents'));
    }

    public function create()
    {
        $instructors = User::where('role', 'instructor')->orderBy('name')->get();
        return view('courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'program' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'course_type' => 'nullable|string|in:program,sae_core,university',
            'is_published' => 'boolean',
            'instructor_id' => 'required|exists:users,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_url'] = Storage::url($request->file('cover_image')->store('courses', 'public'));
        }

        unset($data['cover_image']);

        Course::create($data);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'program' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'course_type' => 'nullable|string|in:program,sae_core,university',
            'status' => 'nullable|in:draft,published',
        ]);

        if ($request->filled('status')) {
            $data['is_published'] = $request->status === 'published';
        }

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image_url) {
                $oldPath = str_replace(Storage::url(''), '', $course->cover_image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['cover_image_url'] = Storage::url($request->file('cover_image')->store('courses', 'public'));
        }

        unset($data['cover_image']);

        $course->update($data);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    public function enroll(Course $course)
    {
        if (!auth()->user()->isStudent()) {
            abort(403);
        }

        $exists = Enrollment::where('student_id', auth()->id())
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        $enrollment = Enrollment::create([
            'student_id' => auth()->id(),
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        auth()->user()->notifications()->create([
            'type'    => 'enrollment',
            'title'   => 'Enrolled: ' . $course->title,
            'message' => 'You have successfully enrolled in ' . $course->title . '.',
            'link'    => route('courses.show', $course),
        ]);

        $course->load('coInstructors');
        $instructorIds = collect([$course->instructor_id])
            ->merge($course->coInstructors->pluck('id'))
            ->unique();

        User::whereIn('id', $instructorIds)->each(function ($instructor) use ($course) {
            $instructor->notifications()->create([
                'type'    => 'enrollment',
                'title'   => 'New Enrollment: ' . $course->title,
                'message' => auth()->user()->name . ' has enrolled in ' . $course->title . '.',
                'link'    => route('courses.roster', $course),
            ]);
        });

        return redirect()->back()->with('success', 'Successfully enrolled in the course.');
    }

    public function unenroll(Course $course)
    {
        if (!auth()->user()->isStudent()) {
            abort(403);
        }

        Enrollment::where('student_id', auth()->id())
            ->where('course_id', $course->id)
            ->delete();

        return redirect()->back()->with('success', 'Successfully unenrolled from the course.');
    }

    public function progress(Course $course, Request $request)
    {
        $user = $request->user() ?? auth()->user();

        $course->loadMissing('modules.lessons', 'assignments', 'students');

        if ($user->isInstructorOrAdmin()) {
            $students = $course->students()->get();

            $allGrades = Grade::with('submission.assignment')
                ->whereIn('submission_id', function ($q) use ($course) {
                    $q->select('id')->from('submissions')
                      ->whereIn('assignment_id', $course->assignments()->pluck('id'));
                })
                ->get()
                ->groupBy('submission.student_id');

            return view('courses.progress', compact('course', 'students', 'allGrades'));
        }

        if (!$course->students()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        $lessonProgress = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $grades = Grade::with('submission.assignment')
            ->whereIn('submission_id', function ($q) use ($user, $course) {
                $q->select('id')->from('submissions')
                  ->where('student_id', $user->id)
                  ->whereIn('assignment_id', $course->assignments()->pluck('id'));
            })
            ->get();

        return view('courses.progress', compact('course', 'lessonProgress', 'grades'));
    }

    public function roster(Course $course)
    {
        $course->load('coInstructors');

        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin() && !$course->coInstructors->contains(auth()->id())) {
            abort(403);
        }

        $students = $course->students()->withPivot('enrolled_at')->latest()->paginate(20);

        return view('courses.roster', compact('course', 'students'));
    }

    public function duplicate(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->load('modules.lessons.topics', 'modules.quizzes', 'modules.liveSessions', 'modules.assignments', 'modules.moduleFiles');

        $newCourse = $course->replicate();
        $newCourse->title = $course->title . ' (Copy)';
        $newCourse->instructor_id = auth()->id();
        $newCourse->is_published = false;
        $newCourse->save();

        foreach ($course->modules as $module) {
            $newModule = $module->replicate();
            $newModule->course_id = $newCourse->id;
            $newModule->save();

            foreach ($module->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->module_id = $newModule->id;
                $newLesson->save();

                foreach ($lesson->topics as $topic) {
                    $newTopic = $topic->replicate();
                    $newTopic->lesson_id = $newLesson->id;
                    $newTopic->save();
                }
            }

            foreach ($module->quizzes as $quiz) {
                $newQuiz = $quiz->replicate();
                $newQuiz->course_id = $newCourse->id;
                $newQuiz->module_id = $newModule->id;
                $newQuiz->save();
            }

            foreach ($module->liveSessions as $session) {
                $newSession = $session->replicate();
                $newSession->course_id = $newCourse->id;
                $newSession->module_id = $newModule->id;
                $newSession->save();
            }

            foreach ($module->assignments as $assignment) {
                $newAssignment = $assignment->replicate();
                $newAssignment->course_id = $newCourse->id;
                $newAssignment->module_id = $newModule->id;
                $newAssignment->save();
            }
        }

        return redirect()->route('courses.edit', $newCourse)
            ->with('success', 'Course duplicated successfully.');
    }

    public function addCoInstructor(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $instructor = User::where('email', $request->email)->firstOrFail();

        if (!$instructor->isInstructor()) {
            return redirect()->back()->with('error', 'User is not an instructor.');
        }

        if ($instructor->id === $course->instructor_id) {
            return redirect()->back()->with('error', 'The main instructor cannot be added as a co-instructor.');
        }

        $exists = $course->coInstructors()->where('instructor_id', $instructor->id)->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'User is already a co-instructor.');
        }

        $course->coInstructors()->attach($instructor->id, ['added_by' => auth()->id()]);

        return redirect()->back()->with('success', 'Co-instructor added successfully.');
    }

    public function removeCoInstructor(Course $course, User $user)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->coInstructors()->detach($user->id);

        return redirect()->back()->with('success', 'Co-instructor removed successfully.');
    }

    public function addStudent(Request $request, Course $course)
    {
        $course->load('coInstructors');

        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin() && !$course->coInstructors->contains(auth()->id())) {
            abort(403);
        }

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:users,id',
        ]);

        $enrolled = 0;
        $skipped = 0;

        foreach ($request->student_ids as $id) {
            $student = User::find($id);
            if (!$student || !$student->isStudent()) {
                $skipped++;
                continue;
            }

            $exists = Enrollment::where('student_id', $id)
                ->where('course_id', $course->id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            Enrollment::create([
                'student_id' => $id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);

            $student->notifications()->create([
                'type'    => 'enrollment',
                'title'   => 'Enrolled: ' . $course->title,
                'message' => 'You have been enrolled in ' . $course->title . '.',
                'link'    => route('courses.show', $course),
            ]);

            $enrolled++;
        }

        $msg = "$enrolled student(s) enrolled successfully.";
        if ($skipped > 0) {
            $msg .= " $skipped skipped (already enrolled or not a student).";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function bulkEnrollCSV(Request $request, Course $course)
    {
        $course->load('coInstructors');

        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin() && !$course->coInstructors->contains(auth()->id())) {
            abort(403);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        $emailIndex = array_search('email', array_map('strtolower', $header));
        if ($emailIndex === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'CSV must contain an "email" column.');
        }

        $enrolled = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $email = trim($row[$emailIndex] ?? '');
            if (empty($email)) continue;

            $student = User::where('email', $email)->first();
            if (!$student || !$student->isStudent()) continue;

            $exists = Enrollment::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->exists();

            if ($exists) continue;

            Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);

            $enrolled++;
        }

        fclose($handle);

        return redirect()->back()->with('success', "$enrolled students enrolled successfully.");
    }

    public function globalRoster()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $courses = Course::with(['students', 'instructor'])->latest()->get();
        } else {
            $taughtIds = $user->taughtCourses()->pluck('id');
            $coTaughtIds = $user->coInstructedCourses()->pluck('course_id');
            $courseIds = $taughtIds->merge($coTaughtIds)->unique();
            $courses = Course::whereIn('id', $courseIds)
                ->with(['students', 'instructor'])
                ->latest()
                ->get();
        }

        return view('courses.roster-index', compact('courses'));
    }
}
