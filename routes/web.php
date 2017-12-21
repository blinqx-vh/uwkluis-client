<?php

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
    return view('welcome');
});


Route::get('/demo/scopes', 'Demo@getScopes');
Route::get('/demo/dossier', 'Demo@getDossier');
Route::get('/demo/postdossier', 'Demo@postDossier');
Route::get('/demo/consumer-connection', 'Demo@getConsumerConnection');
Route::get('/demo/connect', 'Demo@connect');
Route::get('/connect/callback', 'Demo@connectionCallback');