<?php

return [
    'university_name' => env('FINEDU_UNIVERSITY_NAME', 'Universitas Brawijaya'),
    'allowed_email_domain' => env('FINEDU_ALLOWED_EMAIL_DOMAIN', 'student.ub.ac.id'),
    'otp_expires_minutes' => (int) env('FINEDU_OTP_EXPIRES_MINUTES', 10),
];
