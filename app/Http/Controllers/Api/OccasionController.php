<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Occasion;
use Illuminate\Http\Request;

class OccasionController extends Controller
{
    /**
     * Get list of active occasions
     */
    public function index(Request $request)
    {
        $query = Occasion::query()
            ->when($request->active_only, function ($query) {
                return $query->where('is_active', true);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_hi', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%")
                        ->orWhere('description_hi', 'like', "%{$search}%");
                });
            })
            ->withCount('galleries');

        // Sort options
        $sortField = $request->input('sort_by', 'name_en');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return $query->get();
    }

    /**
     * Get a specific occasion with its galleries
     */
    public function show($slug)
    {
        $occasion = Occasion::with(['galleries' => function ($query) {
            $query->with(['images' => function ($q) {
                $q->orderBy('display_order');
            }])
                ->orderBy('event_date', 'desc');
        }])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($occasion);
    }

    /**
     * Get active occasions with gallery counts
     */
    public function activeWithCounts()
    {
        $occasions = Occasion::where('is_active', true)
            ->withCount('galleries')
            ->orderBy('name_en')
            ->get();

        return response()->json($occasions);
    }
}
