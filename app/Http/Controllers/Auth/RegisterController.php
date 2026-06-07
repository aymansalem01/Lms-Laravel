<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * RegisterController
 * Mirrors Next.js: app/(auth)/signup/page.tsx
 *                  app/(auth)/signup/student/page.tsx
 *                  app/(auth)/signup/instructor/page.tsx
 */
class RegisterController extends Controller
{
    const PROGRAMS = [
        'Film Production',
        'Digital Media',
        'Game Design',
        'Audio Engineering',
    ];

    /** GET /signup — role chooser */
    public function choose()
    {
        return view('auth.signup');
    }

    /** GET /signup/student */
    public function studentForm()
    {
        return view('auth.signup-student', ['programs' => self::PROGRAMS]);
    }

    /** GET /signup/instructor */
    public function instructorForm()
    {
        return view('auth.signup-instructor', ['programs' => self::PROGRAMS]);
    }

    /** POST /signup/student */
    public function registerStudent(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'program'               => ['nullable', 'string', 'in:'.implode(',', self::PROGRAMS)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'student',
            'program'  => $data['program'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Welcome to SAE LMS!');
    }

    /** POST /signup/instructor */
    public function registerInstructor(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['required', 'confirmed', Password::min(8)],
            'program'          => ['nullable', 'string', 'in:'.implode(',', self::PROGRAMS)],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'linkedin_url'     => ['nullable', 'url', 'max:500'],
            'website_url'      => ['nullable', 'url', 'max:500'],
            'qualifications'   => ['nullable', 'string', 'max:2000'],
        ]);

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'role'             => 'instructor',
            'program'          => $data['program'] ?? null,
            'bio'              => $data['bio'] ?? null,
            'years_experience' => $data['years_experience'] ?? null,
            'linkedin_url'     => $data['linkedin_url'] ?? null,
            'website_url'      => $data['website_url'] ?? null,
            'qualifications'   => !empty($data['qualifications'])
                ? array_filter(array_map('trim', explode("\n", $data['qualifications'])))
                : null,
            'is_verified'      => false, // pending admin review
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Account created! Your credentials are pending admin verification.');
    }
}
