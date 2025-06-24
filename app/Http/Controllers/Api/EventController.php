<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{

  public function index(Request $request)
  {
    $query = Event::query();

    $year = $request->get('year');
    $month = $request->get('month');

    if ($year && $month) {
      // Filter by full YYYY-MM
      $query->whereRaw("DATE_FORMAT(start_date, '%Y-%m') = ?", ["$year-$month"]);
    } elseif ($year) {
      // Filter by year only
      $query->whereYear('start_date', $year);
    } elseif ($month) {
      // Filter by month only (any year)
      $query->whereRaw("DATE_FORMAT(start_date, '%m') = ?", [$month]);
    }

    if ($request->boolean('banner')) {
      $query->where('show_in_banner', true);
    }

    $events = $query->orderBy('start_date', 'asc')->get()->map(function ($event) {
      return [
        'id' => $event->id,
        'name_en' => $event->name_en,
        'name_hi' => $event->name_hi,
        'description_en' => $event->description_en,
        'description_hi' => $event->description_hi,
        'start_date' => $event->start_date,
        'end_date' => $event->end_date,
        'image' => $event->image ? asset('storage/' . $event->image) : null,
        'show_in_banner' => $event->show_in_banner,
      ];
    });

    return response()->json(['data' => $events]);
  }


  public function show($id)
  {
    $event = Event::findOrFail($id);

    return response()->json([
      'data' => [
        'id' => $event->id,
        'name_en' => $event->name_en,
        'name_hi' => $event->name_hi,
        'description_en' => $event->description_en,
        'description_hi' => $event->description_hi,
        'start_date' => $event->start_date,
        'end_date' => $event->end_date,
        'image' => $event->image ? asset('storage/' . $event->image) : null,
        'show_in_banner' => $event->show_in_banner,
      ]
    ]);
  }
}
