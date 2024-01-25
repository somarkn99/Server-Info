<?php

use Illuminate\Support\Facades\Route;
use Somarkn99\HostInfo\Controllers\HostInfoController;

Route::get('server/info', [HostInfoController::class, 'index']);
