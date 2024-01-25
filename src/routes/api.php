<?php

use Illuminate\Support\Facades\Route;
use Somarkn99\HostInfo\Http\Controllers\HostInfoController;

Route::get('server/info', [HostInfoController::class,'index']);
