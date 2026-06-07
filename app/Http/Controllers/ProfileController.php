<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|url|max:2048',
            'program' => 'nullable|string|max:255',
        ];

        if ($user->isInstructor()) {
            $rules = array_merge($rules, [
                'qualifications' => 'nullable|array',
                'linkedin_url' => 'nullable|url|max:2048',
                'website_url' => 'nullable|url|max:2048',
                'years_experience' => 'nullable|integer|min:0',
            ]);
        }

        $data = $request->validate($rules);

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
