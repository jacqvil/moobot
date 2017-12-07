<?php

/*
|--------------------------------------------------------------------------
| Application config
|--------------------------------------------------------------------------
|
| Define you config values here.
|
*/

return [
    'webhook_verify_token' => env('WEBHOOK_VERIFY_TOKEN'),
    'access_token'         => env('PAGE_ACCESS_TOKEN'),
    'apiai_token'          => env('APIAI_TOKEN'),
    'witai_token'          => env('WITAI_TOKEN'),
];