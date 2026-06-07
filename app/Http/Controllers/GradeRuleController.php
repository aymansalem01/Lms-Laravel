<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class GradeRuleController extends Controller
{
    public function index(Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $course->loadMissing('gradeRules');

        $categories = ['quiz', 'assignment', 'attendance'];
        $weights = [];

        foreach ($categories as $cat) {
            $rule = $course->gradeRules->firstWhere('category', $cat);
            $weights[$cat] = $rule ? $rule->weight : 0;
        }

        return view('grade-rules.index', compact('course', 'weights', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'weights' => 'required|array',
            'weights.quiz' => 'required|numeric|min:0|max:100',
            'weights.assignment' => 'required|numeric|min:0|max:100',
            'weights.attendance' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($data['weights'] as $category => $weight) {
            $course->gradeRules()->updateOrCreate(
                ['category' => $category],
                ['weight' => $weight]
            );
        }

        return redirect()->route('courses.grade-rules.index', $course)->with('success', 'Grade rules updated.');
    }
}
