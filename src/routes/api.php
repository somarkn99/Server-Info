<?php

use Illuminate\Support\Facades\Route;
use somarkn99\hostInfo\Controllers\HostInfoController;

Route::get('server/info', [HostInfoController::class,'index']);
