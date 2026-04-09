<?php

return [
    'default_latitude' => env('CAMPUS_LATITUDE', -6.9883196665620675),
    'default_longitude' => env('CAMPUS_LONGITUDE', 110.43569087874343),
    'default_radius' => env('DEFAULT_RADIUS', 50),
    'qr_refresh_seconds' => env('QR_REFRESH_SECONDS', 120),
    'qr_token_expiry_seconds' => env('QR_TOKEN_EXPIRY', 300),
    'qr_secret_key' => env('QR_SECRET_KEY', 'upgris-absensi-secret-2026'),
    'google_form_url' => env('GOOGLE_FORM_URL', 'https://docs.google.com/forms/d/e/1FAIpQLScWHAvYLOjm1a0QHKNSzR1qkNcWY1NkrTeilbXk_s5p_J_7AQ/viewform'),
];
