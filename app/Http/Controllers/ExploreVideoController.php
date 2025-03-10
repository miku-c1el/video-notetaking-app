<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExploreVideo;

class ExploreVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->expectsJson()) {
            throw new AccessDeniedHttpException();
        }

        $category = $request->input('category');
        $videos = ExploreVideo::where('category', $category)->get();
    
        $formattedVideos = $videos->map(fn($video) => $video->video_resource, true);
    
        return response()->json([
            'videos' => $formattedVideos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
