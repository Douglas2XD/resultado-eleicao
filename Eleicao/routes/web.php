<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EleicaoController;

Route::get('/', [EleicaoController::class, 'index']);
Route::get('/eleicao/ver-resultado/{idEleicao}', [EleicaoController::class, 'verResultado']);
