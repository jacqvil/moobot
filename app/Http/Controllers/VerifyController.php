<?php

namespace MooBot\Http\Controllers;

use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function index()
    {
        return env('MESSENGER_VERIFICATION_CODE');
    }
}
