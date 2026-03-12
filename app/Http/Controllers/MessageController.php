<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('messages.index', ['messages' => auth()->user()->messages()->latest()->get()]);
    }
}
