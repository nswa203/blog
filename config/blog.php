<?php

return [
   /*
    |--------------------------------------------------------------------------
    | NS # Ordnance Survey OpenSpace API key
    |--------------------------------------------------------------------------
    */
    'os_apikey' => env('OS_APIKEY', '74BBA293ABAA3E78E0530C6CA40A9F3F'),

    /*
    |--------------------------------------------------------------------------
    | NS # Google recaptcha account details used within public form submission
    |--------------------------------------------------------------------------
    */
    'captcha_sitekey' => env('CAPTCHA_SITEKEY', '6LfjL18UAAAAAJndLVImcv5hfMo3P3TV9o7puzH9'),
    'captcha_server'  => env('CAPTCHA_SERVER',  'https://www.google.com/recaptcha/api/siteverify'),
    'captcha_secret'  => env('CAPTCHA_SECRET',  '6LfjL18UAAAAAMRoQFB3k2iQfPiS8XmC5zKWNMXO'),
];    
