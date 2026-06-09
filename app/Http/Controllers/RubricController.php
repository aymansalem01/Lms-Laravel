<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Rubric;
use App\Services\RubricImportService;
use Illuminate\Http\Request;

class RubricController extends Controller
{
    public function index(Course $course)
    {
        $user = auth()->user();
        $rubrics = $course->rubrics()
            ->when(!$user->isAdmin(), fn($q) => $q->where('instructor_id', $user->id))
            ->get();
        return view('rubrics.index', compact('course', 'rubrics'));
    }

    public function create(Course $course)
    {
        $user = auth()->user();
        if (!$user->isInstructorOrAdmin()) { abort(403); }
        return view('rubrics.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $user = auth()->user();
        if (!$user->isInstructorOrAdmin()) { abort(403); }

        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'criteria' => 'nullable|json',
            'levels'   => 'nullable|json',
            'cells'    => 'nullable|json',
        ]);

        foreach (['criteria', 'levels', 'cells'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        $data['instructor_id'] = $user->id;

        $course->rubrics()->create($data);

        return redirect()->route('courses.rubrics.index', $course)
            ->with('success', 'Rubric created successfully.');
    }

    public function edit(Course $course, Rubric $rubric)
    {
        $user = auth()->user();
        if (!$user->isInstructorOrAdmin() || ($user->isInstructor() && $rubric->instructor_id !== $user->id)) {
            abort(403);
        }
        return view('rubrics.edit', compact('course', 'rubric'));
    }

    public function update(Request $request, Course $course, Rubric $rubric)
    {
        $user = auth()->user();
        if (!$user->isInstructorOrAdmin() || ($user->isInstructor() && $rubric->instructor_id !== $user->id)) {
            abort(403);
        }

        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'criteria' => 'nullable|json',
            'levels'   => 'nullable|json',
            'cells'    => 'nullable|json',
        ]);

        foreach (['criteria', 'levels', 'cells'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        $rubric->update($data);

        return redirect()->route('courses.rubrics.index', $course)
            ->with('success', 'Rubric updated successfully.');
    }

    public function importXml(Request $request, Course $course)
    {
        $user = auth()->user();
        if (!$user->isInstructorOrAdmin()) { abort(403); }

        $base = ['instructor_id' => $user->id];

        if ($request->hasFile('xml_file')) {
            $request->validate([
                'xml_file' => 'required|file|mimes:xml',
                'title' => 'required|string|max:255',
            ]);

            $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());
            $service = app(RubricImportService::class);
            $parsed = $service->importFromXML($xmlContent);

            $course->rubrics()->create($base + [
                'title' => $request->input('title'),
                'criteria' => $parsed['criteria'],
                'levels' => $parsed['levels'],
                'cells' => $parsed['cells'],
            ]);
        } else {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'criteria' => 'required|json',
                'levels' => 'required|json',
                'cells' => 'required|json',
            ]);

            $course->rubrics()->create($base + [
                'title' => $data['title'],
                'criteria' => json_decode($data['criteria'], true),
                'levels' => json_decode($data['levels'], true),
                'cells' => json_decode($data['cells'], true),
            ]);
        }

        return redirect()->route('courses.rubrics.index', $course)
            ->with('success', __('Rubric imported successfully.'));
    }

    public function destroy(Course $course, Rubric $rubric)
    {
        $user = auth()->user();
        if (!$user->isInstructorOrAdmin() || ($user->isInstructor() && $rubric->instructor_id !== $user->id)) {
            abort(403);
        }
        $rubric->delete();

        return redirect()->route('courses.rubrics.index', $course)
            ->with('success', 'Rubric deleted successfully.');
    }
}
