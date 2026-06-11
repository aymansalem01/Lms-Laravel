<?php

namespace App\Http\Controllers;

use App\Exports\GradeRulesExport;
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

    public function export(Course $course)
    {
        return app(GradeRulesExport::class)->download($course);
    }

    public function downloadExample()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="grade-rules-import-example.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Course', 'Category', 'Weight (%)']);
            fputcsv($handle, ['Introduction to Film', 'Quiz', '30']);
            fputcsv($handle, ['Introduction to Film', 'Assignment', '50']);
            fputcsv($handle, ['Introduction to Film', 'Attendance', '20']);
            fclose($handle);
        };

        return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
    }
}
