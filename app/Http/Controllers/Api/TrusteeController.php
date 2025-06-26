<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trustee;
use Illuminate\Http\Request;

class TrusteeController extends Controller
{

  public function index(Request $request)
  {
    return response()->json([
      'data' => Trustee::select('id', 'name', 'designation', 'email', 'phone', 'address', 'image')->get()
    ]);
  }
}
