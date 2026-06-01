<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'name' => 'FinEdu+ API',
    'status' => 'ready',
]));
