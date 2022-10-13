<?php

use Illuminate\Support\Facades\Route;
use somarkn99\hostInfo\Controllers\HostinfoController;

Route::get('info', [HostinfoController::class,'index']);
