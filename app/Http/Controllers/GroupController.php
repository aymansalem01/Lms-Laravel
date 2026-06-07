<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->load('groups.students');
        $students = $course->students;

        return view('groups.index', compact('course', 'students'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->groups()->create($data);

        return redirect()->route('courses.groups.index', $course)->with('success', 'Group created.');
    }

    public function update(Request $request, Course $course, Group $group)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group->update($data);

        return redirect()->route('courses.groups.index', $course)->with('success', 'Group updated.');
    }

    public function destroy(Course $course, Group $group)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $group->delete();

        return redirect()->route('courses.groups.index', $course)->with('success', 'Group deleted.');
    }

    public function addStudent(Request $request, Course $course, Group $group)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($data['student_id']);

        if (!$course->students()->where('users.id', $student->id)->exists()) {
            return redirect()->back()->with('error', 'Student is not enrolled in this course.');
        }

        if ($group->students()->where('student_id', $student->id)->exists()) {
            return redirect()->back()->with('error', 'Student is already in this group.');
        }

        $group->students()->attach($student->id);

        return redirect()->route('courses.groups.index', $course)->with('success', 'Student added to group.');
    }

    public function removeStudent(Course $course, Group $group, User $student)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $group->students()->detach($student->id);

        return redirect()->route('courses.groups.index', $course)->with('success', 'Student removed from group.');
    }
}
