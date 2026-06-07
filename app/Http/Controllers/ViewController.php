<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function student()
    {
        if (!in_array(auth()->user()->role, ['instructor', 'admin'])) abort(403);
        session(['student_view' => true]);
        return back()->with('success', __('Viewing as student.'));
    }

    public function instructor()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        session(['instructor_view' => true]);
        return back()->with('success', __('Viewing as instructor.'));
    }

    public function exit()
    {
        session()->forget('student_view');
        session()->forget('instructor_view');
        return back()->with('success', __('Exited view mode.'));
    }

    public function theme(Request $request)
    {
        $theme = $request->input('theme', 'dark');
        if (!in_array($theme, ['dark', 'light'])) $theme = 'dark';
        session(['theme' => $theme]);
        if (auth()->check()) {
            auth()->user()->update(['theme' => $theme]);
        }
        return back();
    }
}
