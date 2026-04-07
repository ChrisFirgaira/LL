<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();

        return Inertia::render('Account/Index', [
            'account' => [
                'name' => $user?->name,
                'email' => $user?->email,
            ],
        ]);
    }
}
