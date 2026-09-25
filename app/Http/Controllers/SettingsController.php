<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('passkeys');

        return view('settings.index', compact('user'));
    }
}
