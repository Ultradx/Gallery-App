<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use App\Models\Tag;
use Illuminate\Http\Request;

class ScreenshotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tags = $request->query('tags'); // e.g. ?tags=Jan,Monday,15_min,Bullish

        $query = Screenshot::with('tags');

        if ($tags) {
            $tagArray = explode(',', $tags);

            // Always require these tags
            foreach ($tagArray as $tag) {
                $query->whereHas('tags', fn($q) => $q->where('name', $tag));
            }

            // Special case: exclude "draw" unless it is explicitly in the selected tags
            if (! in_array('draw', $tagArray)) {
                $query->whereDoesntHave('tags', fn($q) => $q->where('name', 'draw'));
            }
        } else {
            // No filters → exclude "draw" by default
            $query->whereDoesntHave('tags', fn($q) => $q->where('name', 'draw'));
        }

        return $query->get();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'nullable|string',
            'tags'  => 'array'
        ]);

        // Store file
        $path = $request->file('image')->store('screenshots', 'public');

        // Save to DB
        $screenshot = Screenshot::create([
            'title' => $request->title,
            'file_path' => $path,
        ]);

        // Attach tags
        if ($request->has('tags')) {
            $tagIds = collect($request->tags)->map(function ($name) {
                return Tag::firstOrCreate(['name' => $name])->id;
            });
            $screenshot->tags()->sync($tagIds);
        }

        return response()->json($screenshot->load('tags'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
