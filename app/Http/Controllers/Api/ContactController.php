<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Mail\ContactReceivedMail;
use App\Mail\ContactAdminMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|min:10',
        ]);

        $msg = ContactMessage::create([
            ...$data,
            'ip_address' => $request->ip(),
        ]);

        // Email de confirmation à l'expéditeur
        try {
            Mail::to($msg->email)->send(new ContactReceivedMail($msg));
        } catch (\Exception $e) {
            logger('Contact confirmation mail failed: ' . $e->getMessage());
        }

        // Email notification à l'admin
        try {
            Mail::to(config('app.admin_email'))->send(new ContactAdminMail($msg));
        } catch (\Exception $e) {
            logger('Contact admin mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Message envoyé ! Nous vous répondrons sous 24h.',
        ], 201);
    }
}
