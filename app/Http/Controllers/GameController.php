<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{
    private $apiKey = 'd8c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0'; // Chave gratuita da RAWG
    private $baseUrl = 'https://api.rawg.io/api';

    public function index()
    {
        $response = Http::get("{$this->baseUrl}/games", [
            'key' => $this->apiKey,
            'page_size' => 20,
            'ordering' => '-rating'
        ]);

        $games = $response->json()['results'] ?? [];
        
        return view('games.index', compact('games'));
    }

    public function show($id)
    {
        $response = Http::get("{$this->baseUrl}/games/{$id}", [
            'key' => $this->apiKey
        ]);

        $game = $response->json();
        
        return view('games.show', compact('game'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return view('games.search', ['games' => [], 'query' => '']);
        }

        $response = Http::get("{$this->baseUrl}/games", [
            'key' => $this->apiKey,
            'search' => $query,
            'page_size' => 20
        ]);

        $games = $response->json()['results'] ?? [];
        
        return view('games.search', compact('games', 'query'));
    }

    public function getGames()
    {
        $response = Http::get("{$this->baseUrl}/games", [
            'key' => $this->apiKey,
            'page_size' => 20,
            'ordering' => '-rating'
        ]);

        return response()->json($response->json()['results'] ?? []);
    }

    public function getGame($id)
    {
        $response = Http::get("{$this->baseUrl}/games/{$id}", [
            'key' => $this->apiKey
        ]);

        return response()->json($response->json());
    }
} 