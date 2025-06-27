<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:100',
      'email' => 'required|email',
      'message' => 'required|string|max:1000',
    ]);
    Contact::create($validated);
    // Send an email (optional)
    Mail::raw($validated['message'], function ($mail) use ($validated) {
      $mail->to('sagarsaggy615@gmail.com')
        ->subject('New Contact Message')
        ->replyTo($validated['email'])
        ->from($validated['email'], $validated['name']);
    });

    return response()->json(['success' => true, 'message' => 'Message sent successfully']);
  }
}
