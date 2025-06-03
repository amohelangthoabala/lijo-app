<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => 'Homepage or static view');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
