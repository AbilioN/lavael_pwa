<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
Route::get('/search', [GameController::class, 'search'])->name('games.search');

// API routes for AJAX
Route::get('/api/games', [GameController::class, 'getGames'])->name('api.games');
Route::get('/api/games/{id}', [GameController::class, 'getGame'])->name('api.game');
