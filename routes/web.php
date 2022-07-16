<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('form');
});

Route::post('/', function (\App\Requests\AccountBalancingRequest $request) {

    $accountBalancingService = app(\App\Services\AccountBalancingService::class);

    $currentState = json_decode($request->get('current_state', []), true);
    $desiredState = json_decode($request->get('desired_state', []), true);
    $movements = $accountBalancingService->getMovements($currentState, $desiredState);

    return view('form', [
        'request' => $request,
        'movements' => json_encode($movements, JSON_PRETTY_PRINT)
    ]);
});
