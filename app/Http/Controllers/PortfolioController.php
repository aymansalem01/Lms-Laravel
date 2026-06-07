<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function show(User $user)
    {
        $items = PortfolioItem::where('student_id', $user->id)
            ->where(function ($q) {
                $q->where('is_public', true);
                if (auth()->check() && (auth()->id() === request()->route('user')->id || auth()->user()->isAdmin())) {
                    $q->orWhere('is_public', false);
                }
            })
            ->latest()
            ->get();

        return view('portfolio.show', compact('user', 'items') + ['portfolioItems' => $items]);
    }

    public function edit()
    {
        $user = auth()->user();
        $items = PortfolioItem::where('student_id', $user->id)->latest()->get();
        return view('portfolio.edit', compact('user', 'items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media_url' => 'nullable|string|max:2048',
            'media_file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,mp3,pdf,doc,docx',
            'media_type' => 'nullable|in:video,audio,image,file',
            'is_public' => 'boolean',
        ]);

        $data['student_id'] = auth()->id();
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('uploads/portfolio', 'public');
            $data['media_url'] = $path;
            if (!$data['media_type']) {
                $data['media_type'] = match ($request->file('media_file')->getMimeType()) {
                    'video/mp4', 'video/webm', 'video/ogg' => 'video',
                    'audio/mpeg', 'audio/ogg', 'audio/wav' => 'audio',
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp' => 'image',
                    default => 'file',
                };
            }
        }

        PortfolioItem::create($data);

        return redirect()->route('portfolio.edit')->with('success', 'Portfolio item added!');
    }

    public function destroy(PortfolioItem $item)
    {
        if ($item->student_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        $item->delete();
        return back()->with('success', 'Portfolio item deleted.');
    }
}
