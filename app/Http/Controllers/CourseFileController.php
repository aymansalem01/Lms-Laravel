<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseFile;
use Illuminate\Http\Request;

class CourseFileController extends Controller
{
    public function index(Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $files = $course->files()->latest()->get();

        return view('courses.files.index', compact('course', 'files'));
    }

    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'filename' => 'required|string|max:255',
            'file_path' => 'required|string|max:2048',
            'mime_type' => 'nullable|string|max:50',
        ]);

        $data['original_filename'] = $data['filename'];
        $data['uploaded_by'] = auth()->id();

        $course->files()->create($data);

        return redirect()->route('courses.files.index', $course)
            ->with('success', __('File added successfully.'));
    }

    public function destroy(Course $course, CourseFile $file)
    {
        if (!auth()->user()->isInstructorOrAdmin()) { abort(403); }

        $file->delete();

        return redirect()->route('courses.files.index', $course)
            ->with('success', __('File removed successfully.'));
    }
}
