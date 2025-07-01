<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Darshan;
use Illuminate\Http\Request;

class DarshanController extends Controller
{
    public function index()
    {
        return response()->json(
            Darshan::latest()->get()
        );
    }

    public function show($id)
    {
        $video = Darshan::find($id);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        return response()->json($video);
    }
}
