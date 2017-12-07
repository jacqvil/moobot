<?php

namespace MooBot\Http\Controllers;

use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('hub_verify_token')) {
            return $request->get('hub_challenge');
        } else {
            return 'Invalid Verify Token';
        }

    }
}
