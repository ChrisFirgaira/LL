<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\StorefrontContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contact/Index', [
            'contactDetails' => [
                'email' => config('storefront.contact.display_email'),
                'phone' => config('storefront.contact.display_phone'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        try {
            Mail::to(
                config('storefront.contact.to_address'),
                config('storefront.contact.to_name')
            )->send(new StorefrontContactMessage($data));
        } catch (Throwable $exception) {
            Log::error('Storefront contact form delivery failed.', [
                'message' => $exception->getMessage(),
                'email' => $data['email'],
            ]);

            return back()
                ->withInput()
                ->with('error', 'Your message could not be sent right now. Please try again shortly.');
        }

        return back()->with('success', 'Thanks for reaching out. Your message has been sent.');
    }
}
