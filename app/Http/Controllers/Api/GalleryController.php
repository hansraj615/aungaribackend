<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Get list of galleries with optional filtering
     */
    public function index(Request $request)
    {
        $query = Gallery::with(['images', 'occasion'])
            ->when($request->occasion_id, function ($query, $occasionId) {
                return $query->where('occasion_id', $occasionId);
            })
            ->when($request->is_featured, function ($query) {
                return $query->where('is_featured', true);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title_en', 'like', "%{$search}%")
                        ->orWhere('title_hi', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%")
                        ->orWhere('description_hi', 'like', "%{$search}%");
                });
            })
            ->when($request->from_date, function ($query, $fromDate) {
                return $query->where('event_date', '>=', $fromDate);
            })
            ->when($request->to_date, function ($query, $toDate) {
                return $query->where('event_date', '<=', $toDate);
            });

        // Sort options
        $sortField = $request->input('sort_by', 'event_date');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 12);
        return $query->paginate($perPage);
    }

    /**
     * Get a specific gallery with its images
     */
    public function show($id)
    {
        $gallery = Gallery::with(['images' => function ($query) {
            $query->orderBy('display_order');
        }, 'occasion'])->findOrFail($id);

        return response()->json($gallery);
    }

    /**
     * Get featured galleries
     */
    public function featured()
    {
        $galleries = Gallery::with(['images' => function ($query) {
            $query->orderBy('display_order');
        }, 'occasion'])
            ->where('is_featured', true)
            ->orderBy('display_order')
            ->get();

        return response()->json($galleries);
    }

    /**
     * Get latest galleries
     */
    public function latest()
    {
        $galleries = Gallery::with(['images' => function ($query) {
            $query->orderBy('display_order');
        }, 'occasion'])
            ->latest('event_date')
            ->take(6)
            ->get();

        return response()->json($galleries);
    }
}
